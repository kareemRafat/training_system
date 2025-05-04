<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Group;
use App\Models\Branch;
use Filament\Forms\Form;
use App\Models\Instructor;
use Filament\Tables\Table;
use App\Models\RepeatedStudent;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Enums\FiltersLayout;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Actions\NormalActions\AddCommentAction;
use App\Filament\Actions\NormalActions\ShowCommentAction;
use App\Filament\Resources\RepeatedStudentResource\Pages;
use App\Filament\Actions\NormalActions\RepeatStudentsActions\RepeatAccepted;

class RepeatedStudentResource extends Resource
{
    protected static ?string $model = RepeatedStudent::class;

    protected static ?string $navigationGroup = 'طلبات الإعادة';

    protected static ?string $navigationLabel = 'طـلـبـات الإعـادة ( Waiting )';

    protected static ?string $modelLabel = 'طالب'; // Singular

    protected static ?string $pluralModelLabel = 'الطلاب المؤجلين'; // Plural

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $activeNavigationIcon = 'heroicon-s-clock';

    protected static ?int $navigationSort = 1; // Position in sidebar

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('اسم الطالب')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->live()
                    ->afterStateUpdated(function ($livewire, $component) {
                        // live validation ->live()
                        $livewire->validateOnly($component->getStatePath());
                    }),
                Forms\Components\TextInput::make('phone')
                    ->label('رقم الهاتف')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->rules('required|phone:' . config('app.PHONE_COUNTRIES'))
                    ->live()
                    ->afterStateUpdated(function ($livewire, $component) {
                        // live validation
                        $livewire->validateOnly($component->getStatePath());
                    }),
                Forms\Components\Select::make('track_start')
                    ->label('إعادة من ...')
                    ->native(false)
                    ->required()
                    ->options([
                        'html' => 'HTML',
                        'css' => 'CSS',
                        'javascript' => 'JavaScript',
                        'php' => 'PHP',
                        'project' => 'Project',
                        'mysql' => 'MySQL',
                    ]),
                Forms\Components\Select::make('branch_id')
                    ->label('اسم الفرع')
                    ->native(false)
                    ->required()
                    ->options(
                        Branch::when(
                            Auth::check() && Auth::user()->branch_id,
                            function (Builder $query) {
                                $query
                                    ->where('branch_id', Auth::user()->branch_id);
                            }
                        )
                            ->pluck('name', 'id')
                    )
                    ->default(fn() => Auth::user()->branch_id),
                Forms\Components\Select::make('group_id')
                    ->label('المجموعة السابقة')
                    ->native(false)
                    ->required()
                    ->searchable()
                    ->options(
                        Group::when(
                            Auth::check() && Auth::user()->branch_id,
                            function (Builder $query) {
                                $query
                                    ->where('branch_id', Auth::user()->branch_id);
                            }
                        )
                            ->pluck('name', 'id')
                    ),
                Forms\Components\Select::make('instructor_id')
                    ->label('اسم المحاضر المطلوب')
                    ->native(false)
                    ->helperText('فى حالة عدم تحديد المحاضر يرجى ترك الحقل فارغاً')
                    ->options(
                        Instructor::when(
                            Auth::check() && Auth::user()->branch_id,
                            function (Builder $query) {
                                $query
                                    ->where('branch_id', Auth::user()->branch_id);
                            }
                        )
                            ->whereActive(true)
                            ->pluck('name', 'id')
                    )
                    ->getOptionLabelUsing(function ($value) {
                        // When Update Get the name of the selected instructor, even if inactive
                        $instructor = Instructor::find($value);
                        return $instructor->active
                            ? $instructor->name
                            : "المحاضر {$instructor->name} غير متاح";
                    }),
                Forms\Components\ToggleButtons::make('repeat_status')
                    ->label('حالة الطلب')
                    ->required()
                    ->default('waiting')
                    ->options([
                        'waiting' => 'Waiting',
                        'accepted' => 'Accepted',
                    ])
                    ->inline()
                    ->colors([
                        'waiting' => 'warning',
                        'accepted' => 'success',
                    ])
                    ->icons([
                        'waiting' => 'heroicon-o-clock',
                        'accepted' => 'heroicon-o-check-circle',
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(
                fn(Builder $query) => $query
                    ->when(Auth::check() && Auth::user()->branch_id, function (Builder $query) {
                        $query->where('branch_id', Auth::user()->branch_id);
                    })
                    ->whereRepeatStatus('waiting')
                    ->withCount('comments')
            )
            ->defaultPaginationPageOption(25)
            ->recordAction(null) // prevent clickable row
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('اسم الطالب')
                    ->searchable()
                    ->fontFamily(FontFamily::Sans)
                    ->color('violet')
                    ->weight(FontWeight::Medium)
                    ->tooltip(function ($record) {
                        $groupName = ucwords($record->group->name);

                        return "اسم الجروب : {$groupName}";
                    }),
                Tables\Columns\TextColumn::make('phone')
                    ->label('رقم الهاتف')
                    ->searchable(),
                Tables\Columns\TextColumn::make('group.name')
                    ->label('المجموعة')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->weight(FontWeight::Medium)
                    ->extraAttributes([
                        'style' => 'text-transform:capitalize',
                    ]),
                Tables\Columns\TextColumn::make('track_start')
                    ->label('إعادة من')
                    ->extraAttributes([
                        'style' => 'padding: 6px 8px; border-radius: 6px;width:fit-content;',
                        'class' => 'border border-[#f1d9d9] dark:bg-gray-200 bg-white-200 dark:border-gray-700',
                    ])
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'html' => 'HTML',
                        'css' => 'CSS',
                        'javascript' => 'JavaScript',
                        'php' => 'PHP',
                        'project' => 'Project',
                        'mysql' => 'MySQL',
                        default => $state,
                    })
                    ->color(fn($state) => match ($state) {
                        'html' => 'success',
                        'css' => 'danger',
                        'javascript' => 'warning',
                        'php' => 'primary',
                        'project' => 'violet',
                        'mysql' => 'info',
                        default => 'default',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الطلب')
                    ->date('d - m - Y')
                    ->dateTooltip(function ($record) {
                        $date = Carbon::parse($record->created_at)->diffForHumans();

                        return "{$date}";
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('instructor.name')
                    ->label('اسم المحاضر المطلوب')
                    ->formatStateUsing(function ($state, $record) {
                        if (is_null($record->instructor_id) || ! $record->instructor->active) {
                            return 'غير محدد أو غير نشط';
                        }

                        return $state;
                    })
                    ->color(function ($state, $record) {
                        if (is_null($record->instructor_id) || ! $record->instructor->active) {
                            return 'danger';
                        }

                        return 'default';
                    })
                    ->default('لايوجد محاضر مطلوب'),
                Tables\Columns\TextColumn::make('branch.name')
                    ->label('الفرع')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->weight(FontWeight::Medium)
                    ->extraAttributes([
                        'style' => 'text-transform:capitalize',
                    ])
                    ->visible(fn() => Auth::check() && is_null(Auth::user()->branch_id)),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('track_start')
                    ->label('بداية الإعادة')
                    ->native(false)
                    ->options([
                        'html' => 'HTML',
                        'css' => 'CSS',
                        'javascript' => 'JavaScript',
                        'php' => 'PHP',
                        'project' => 'Project',
                        'mysql' => 'MySQL',
                    ]),
                Tables\Filters\SelectFilter::make('instructor_id')
                    ->label('المحاضر المطلوب')
                    ->native(false)
                    ->relationship(
                        'instructor',
                        'name',
                        fn(Builder $query) => $query->when(Auth::check() && Auth::user()->branch_id, function (Builder $query) {
                            $query
                                ->where('branch_id', Auth::user()->branch_id);
                        })->whereActive(true)
                    ),
                Tables\Filters\SelectFilter::make('branch_id')
                    ->label('الفرع')
                    ->native(false)
                    ->relationship('branch', 'name')
                    ->visible(fn() => Auth::check() && is_null(Auth::user()->branch_id)),
            ], layout: FiltersLayout::AboveContent)

            ->actions([

                AddCommentAction::make('AddComment'),
                ShowCommentAction::make('ShowComment')
                    ->viewCommentsClass(\App\Filament\Resources\RepeatedStudentResource\Pages\ViewComments::class),

                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    RepeatAccepted::make(),
                ])
                    ->tooltip('الإجراءات')
                    ->label('المزيد')
                    ->button()
                    ->size(ActionSize::Small)
                    ->extraAttributes(['class' => 'font-bold text-xs p-1']),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRepeatedStudents::route('/'),
            'view-comments' => Pages\ViewComments::route('/{record}/comments'),
        ];
    }
}
