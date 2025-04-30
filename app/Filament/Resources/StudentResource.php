<?php

namespace App\Filament\Resources;

use App\Filament\Actions\BulkActions\UpdateStatusBulkAction;
use App\Filament\Actions\BulkActions\UpdateTrainingGroupBulkAction;
use App\Filament\Actions\NormalActions\AddCommentAction;
use App\Filament\Actions\NormalActions\ShowCommentAction;
use App\Filament\Actions\NormalActions\UpdateTrainingGroupAction;
use App\Filament\Resources\StudentResource\Pages;
use App\Models\Group;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationGroup = 'التدريب';

    protected static ?string $navigationLabel = 'طـلـبـات الـتـدريـب ( Waiting )';

    protected static ?string $modelLabel = 'طالب'; // Singular

    protected static ?string $pluralModelLabel = 'الطلاب المسجلين ( Waiting )'; // Plural

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $activeNavigationIcon = 'heroicon-s-identification';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('اسم الطالب')
                    ->columnSpan('full') // Make the input take the whole line
                    ->unique(ignoreRecord: true)
                    ->rules('required')
                    ->live()
                    ->afterStateUpdated(function ($livewire, $component) {
                        // live validation
                        $livewire->validateOnly($component->getStatePath());
                    }),
                Forms\Components\Select::make('group_id')
                    ->required()
                    ->label('المجموعة')
                    ->validationMessages([
                        'required' => 'اسم المجموعة مطلوب',
                    ])
                    ->searchable()
                    ->options(
                        Group::when(
                            Auth::check() && Auth::user()->branch_id,
                            fn ($query) => $query->where('branch_id', Auth::user()->branch_id),
                            fn ($query) => $query // else show all groups
                        )
                            ->pluck('name', 'id')
                    )
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Get the branch_id from the selected group
                        // and set it into branch_id hidden form field
                        $group = \App\Models\Group::find($state);
                        if ($group) {
                            $set('branch_id', $group->branch_id);
                        }
                    }),
                Forms\Components\Hidden::make('branch_id'),
                Forms\Components\TextInput::make('phone')
                    ->required()
                    ->label('رقم الهاتف')
                    ->type('tel')
                    ->unique(ignoreRecord: true)
                    ->inputMode('tel')
                    ->helperText('يجب أن يكون الرقم مكون من 11 رقم')
                    ->rules('required|phone:'.config('app.PHONE_COUNTRIES'))
                    ->live()
                    ->afterStateUpdated(function ($livewire, $component) {
                        // live validation
                        $livewire->validateOnly($component->getStatePath());
                    }),
                Forms\Components\Radio::make('start')
                    ->required()
                    ->label('ملاحظات البداية')
                    ->options([
                        'directly' => 'مباشرة',
                        'delay' => 'تأجيل',
                    ])
                    ->descriptions([
                        'directly' => 'بدء التدريب عند اقرب مجموعة',
                        'delay' => 'تأجيل التدريب ',
                    ])
                    ->default('delay')
                    ->inline()
                    ->inlineLabel(false)
                    ->extraAttributes([
                        'style' => 'border: 2px solid #cccccc75; border-radius: 10px; padding: 10px;',
                    ]),

                Forms\Components\Radio::make('status')
                    ->required()
                    ->label('الحالة')
                    ->options([
                        'normal' => 'عادي',
                        'important' => 'مستعجل',
                    ])
                    ->descriptions([
                        'normal' => 'انتهي من الكورس',
                        'important' => 'مستعجل على بداية التدريب',
                    ])
                    ->default('normal')
                    ->inline()
                    ->inlineLabel(false)
                    ->extraAttributes([
                        'style' => 'border: 2px solid #cccccc75; border-radius: 10px; padding: 10px;',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // show only the branch stuff to the employee
            ->modifyQueryUsing(
                fn (Builder $query) => $query
                    ->when(Auth::check() && Auth::user()->branch_id, function (Builder $query) {
                        $query->where('branch_id', Auth::user()->branch_id);
                    })
                    ->whereNull('training_group_id')
                    ->withCount('comments')
            )
            ->defaultPaginationPageOption(25)
            ->recordAction(null) // prevent clickable row
            ->filtersFormColumns(3)
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('اسم الطالب')
                    ->searchable()
                    ->fontFamily(FontFamily::Sans)
                    ->color('violet')
                    ->weight(FontWeight::Medium)
                    ->copyable()
                    ->copyMessage('تم نسخ الاسم')
                    ->tooltip(function ($record) {
                        $groupName = ucwords($record->group->name);

                        return "اسم الجروب : {$groupName}";
                    }),
                Tables\Columns\TextColumn::make('phone')
                    ->label('رقم الموبايل')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('تم نسخ الرقم'),
                Tables\Columns\TextColumn::make('start')
                    ->label('ملاحظات البداية')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'directly' => 'success',
                        'delay' => 'warning',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'directly' => 'مـبـاشـر',
                        'delay' => 'تـأجـيـل',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('group.name')
                    ->label('المجموعة')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->weight(FontWeight::Medium)
                    ->extraAttributes([
                        'style' => 'text-transform:capitalize',
                    ]),

                Tables\Columns\TextColumn::make('group.end_date')
                    ->label('تاريخ الانتهاء')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'normal' => 'success',
                        'important' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'normal' => 'عـادي',
                        'important' => 'مـسـتـعـجـل',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('branch.name')
                    ->label('الفرع')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->weight(FontWeight::Medium)
                    ->extraAttributes([
                        'style' => 'text-transform:capitalize',
                    ])
                    ->visible(fn () => Auth::check() && is_null(Auth::user()->branch_id)),
            ])

            ->filters([
                Filter::make('status')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'important'))
                    ->toggle()
                    ->label('المستعجلين')
                    ->columnSpanFull(),
                SelectFilter::make('start')
                    ->native(false) // use native select
                    ->options([
                        'directly' => 'مباشرة',
                        'delay' => 'تأجيل',
                    ])
                    ->attribute('start')
                    ->label('ملاحظات البداية'),
                SelectFilter::make('group')
                    ->searchable()
                    ->options(
                        Group::when(
                            Auth::check() && Auth::user()->branch_id,
                            fn ($query) => $query->where('branch_id', Auth::user()->branch_id),
                            fn ($query) => $query // else show all groups
                        )
                            ->pluck('name', 'id')
                    )
                    ->attribute('group_id')
                    ->label('المجموعة'),
                SelectFilter::make('branch')
                    ->label('الفرع')
                    ->native(false)
                    ->relationship('branch', 'name')
                    ->visible(fn () => Auth::check() && is_null(Auth::user()->branch_id)),
            ], layout: FiltersLayout::AboveContent)

            ->actions([
                // table row action
                AddCommentAction::make('AddComment'),
                ShowCommentAction::make('ShowComment')
                    ->viewCommentsClass(\App\Filament\Resources\StudentResource\Pages\ViewComments::class),

                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    UpdateTrainingGroupAction::make('UpdateTrainingGroup'),
                ])
                    ->tooltip('الإجراءات')
                    ->label('المزيد')
                    ->button()
                    ->size(ActionSize::Small)
                    ->extraAttributes(['class' => 'font-bold text-xs p-1']),
            ])

            ->bulkActions([
                // all rows action
                // Tables\Actions\BulkActionGroup::make([]),
                UpdateTrainingGroupBulkAction::make(),
                UpdateStatusBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'view-comments' => Pages\ViewComments::route('/{record}/comments'),
            'add-student' => Pages\AddStudents::route('/manage-students'),
        ];
    }
}
