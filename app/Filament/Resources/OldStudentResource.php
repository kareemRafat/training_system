<?php

namespace App\Filament\Resources;

use App\Filament\Actions\NormalActions\AddCommentAction;
use App\Filament\Actions\NormalActions\RemoveFromTrainingAction;
use App\Filament\Actions\NormalActions\ShowCommentAction;
use App\Filament\Actions\NormalActions\UpdateTrainingGroupAction;
use App\Filament\Resources\OldStudentResource\Pages;
use App\Models\Group;
use App\Models\Student;
use App\Models\TrainingGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class OldStudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationGroup = 'التدريب';

    protected static ?string $navigationLabel = 'جـمـيـع الـطـلاب';

    protected static ?string $modelLabel = 'طالب'; // Singular

    protected static ?string $pluralModelLabel = 'جميع الطلاب'; // Plural

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $activeNavigationIcon = 'heroicon-s-users';

    protected static ?int $navigationSort = 4; // Position in sidebar

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->unique(ignoreRecord: true)
                ->label('اسم الطالب')
                ->live()
                ->afterStateUpdated(function ($livewire, $component) {
                    // live validation
                    $livewire->validateOnly($component->getStatePath());
                }),
            Forms\Components\Select::make('training_group_id')
                ->required(fn(callable $get) => $get('training_group_id') !== null)
                ->label('مجموعة التدريب')
                ->searchable()
                ->options(
                    TrainingGroup::when(
                        Auth::check() && Auth::user()->branch_id,
                        fn($query) => $query->where('branch_id', Auth::user()->branch_id),
                        fn($query) => $query // else show all groups
                    )
                        ->limit(6)
                        ->pluck('name', 'id')
                ),
            Forms\Components\TextInput::make('phone')
                ->required()
                ->label('رقم الهاتف')
                ->type('tel')
                ->unique(ignoreRecord: true)
                ->validationMessages([
                    'unique' => 'الهاتف مسجل مسبقاً',
                ])
                ->inputMode('tel')
                ->live()
                ->helperText('يجب أن يكون الرقم مكون من 11 رقم')
                ->afterStateUpdated(function ($state, callable $set, $livewire, $component) {
                    $set('phone', preg_replace('/[^0-9]/', '', $state));
                    // live validation
                    $livewire->validateOnly($component->getStatePath());
                }),
            Forms\Components\Select::make('group_id')
                ->required()
                ->label('المجموعة')
                ->searchable()
                ->options(
                    Group::when(
                        Auth::check() && Auth::user()->branch_id,
                        fn($query) => $query->where('branch_id', Auth::user()->branch_id),
                        fn($query) => $query // else show all groups
                    )
                        ->pluck('name', 'id')
                ),
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
                fn(Builder $query) => $query->when(Auth::check() && Auth::user()->branch_id, function (Builder $query) {
                    $query->where('branch_id', Auth::user()->branch_id);
                })
                    ->withCount('comments'),
            )
            ->defaultPaginationPageOption(25)
            ->recordAction(null) // prevent     clickable row
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
                    ->copyMessage('تم نسخ الاسم'),
                Tables\Columns\TextColumn::make('phone')
                    ->label('رقم الموبايل')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('تم نسخ الرقم'),
                Tables\Columns\TextColumn::make('start')
                    ->label('ملاحظات البداية')
                    ->badge()
                    ->color(
                        fn(string $state): string => match ($state) {
                            'directly' => 'success',
                            'delay' => 'warning',
                        },
                    )
                    ->formatStateUsing(
                        fn(string $state): string => match ($state) {
                            'directly' => 'مـبـاشـر',
                            'delay' => 'تـأجـيـل',
                            default => $state,
                        },
                    ),
                Tables\Columns\TextColumn::make('group.name')
                    ->label('المجموعة'),
                Tables\Columns\TextColumn::make('group.end_date')
                    ->label('تاريخ الانتهاء')
                    ->sortable(),
                Tables\Columns\TextColumn::make('training_group.name')
                    ->label('جروب التدريب')
                    ->default('لم يتدرب بعد')
                    ->badge()
                    ->weight(FontWeight::SemiBold),
            ])
            ->filters([
                Filter::make('training_group_id')
                    ->query(fn(Builder $query): Builder => $query->whereNull('training_group_id'))
                    ->toggle()
                    ->label('الغير متدربين')
                    ->columnSpanFull(),
                SelectFilter::make('start')
                    ->native(false)
                    ->options([
                        'directly' => 'مباشرة',
                        'delay' => 'تأجيل',
                    ])
                    ->attribute('start')
                    ->label('ملاحظات البداية'),
                SelectFilter::make('group')
                    ->native(false)
                    ->options(
                        Group::when(
                            Auth::check() && Auth::user()->branch_id,
                            fn($query) => $query->where('branch_id', Auth::user()->branch_id),
                            fn($query) => $query // else show all groups
                        )
                            ->pluck('name', 'id')
                    )
                    ->attribute('group_id')
                    ->label('مجموعة الكورس'),
                SelectFilter::make('training_group')
                    ->native(false)
                    ->options(
                        TrainingGroup::when(
                            Auth::check() && Auth::user()->branch_id,
                            fn($query) => $query->where('branch_id', Auth::user()->branch_id),
                            fn($query) => $query // else show all groups
                        )
                            ->orderBy('start_date', 'desc')
                            ->pluck('name', 'id')
                    )
                    ->attribute('training_group_id')
                    ->label('جروب التدريب'),
                SelectFilter::make('branch')
                    ->label('الفرع')
                    ->native(false)
                    ->relationship('branch', 'name')
                    ->visible(fn() => Auth::check() && is_null(Auth::user()->branch_id)),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(2)
            ->filtersTriggerAction(
                fn(Action $action) => $action
                    ->button()
                    ->label('الفلاتر'),
            )
            ->actions([
                AddCommentAction::make('AddComment'),
                ShowCommentAction::make('ShowComment')
                    ->viewCommentsClass(\App\Filament\Resources\StudentResource\Pages\ViewComments::class),

                // action group
                ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->color('primary'),

                    // remove from training_group if there is a training_group
                    RemoveFromTrainingAction::make('removeFromTraining'),

                    // add to training_group if there is not training_group
                    UpdateTrainingGroupAction::make('UpdateTrainingGroup'),

                ])
                    ->tooltip('الإجراءات')
                    ->label('المزيد')
                    ->button()
                    ->size(ActionSize::Small)
                    ->extraAttributes(['class' => 'font-bold text-xs p-1']),
            ])

            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([])
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
            'index' => Pages\ListOldStudents::route('/'),
        ];
    }
}
