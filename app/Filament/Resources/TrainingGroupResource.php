<?php

namespace App\Filament\Resources;

use App\Filament\Actions\NormalActions\TrainingGroupActions\PdfAction;
use App\Filament\Resources\TrainingGroupResource\Pages;
use App\Filament\Resources\TrainingGroupResource\Pages\ViewStudents;
use App\Models\Branch;
use App\Models\Instructor;
use App\Models\TrainingGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TrainingGroupResource extends Resource
{
    protected static ?string $model = TrainingGroup::class;

    protected static ?string $navigationGroup = 'التدريب';

    protected static ?string $navigationLabel = 'مـجـمـوعـات الـتـدريـب';

    protected static ?string $modelLabel = 'مجموعة تدريب'; // Singular

    protected static ?string $pluralModelLabel = 'مجموعات التدريب'; // Plural

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $activeNavigationIcon = 'heroicon-s-rectangle-stack';

    public static function form(Form $form): Form
    {

        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('اسم المجموعة')
                    ->unique(ignoreRecord: true)
                    ->validationMessages([
                        'unique' => 'الإسم مسجل مسبقاً',
                    ])
                    ->live()
                    ->afterStateUpdated(function ($livewire, $component) {
                        $livewire->validateOnly($component->getStatePath());
                    })
                    ->helperText('اسم المجموعة يجب ان يكون غير مستخدم من قبل'),
                Forms\Components\DatePicker::make('start_date')
                    ->required()
                    ->label('تاريخ البدء')
                    ->helperText('سيتم تحديد تاريخ الإنتهاء بعد شهرين ونصف من تاريخ البداية')
                    ->afterStateUpdated(function (\Filament\Forms\Set $set, $state) {
                        if ($state) {
                            // add two months and 15 days to the start date ->to end date
                            $set('end_date', \Carbon\Carbon::parse($state)->addMonths(2)->addDays(15));
                        }
                    }),
                Forms\Components\Hidden::make('end_date'),
                Forms\Components\Select::make('instructor_id')
                    ->native(false)
                    ->required()
                    ->label('المحاضر')
                    ->options(
                        Instructor::where('active', true)
                            ->when(
                                Auth::check() && Auth::user()->branch_id,
                                fn ($query) => $query->where('branch_id', Auth::user()->branch_id),
                                fn ($query) => $query // else show all instructors
                            )
                            ->pluck('name', 'id')
                    )
                    ->getOptionLabelUsing(function ($value) {
                        // When Update Get the name of the selected instructor, even if inactive
                        $instructor = Instructor::find($value);

                        return $instructor->active ? $instructor->name : "المحاضر $instructor->name غير متاح";
                    }),
                Forms\Components\Select::make('branch_id')
                    ->native(false) // disable native select
                    ->required()
                    ->label('الفرع')
                    ->options(
                        Branch::when(
                            Auth::check() && Auth::user()->branch_id,
                            fn ($query) => $query->where('id', Auth::user()->branch_id),
                            fn ($query) => $query // else show all groups
                        )
                            ->pluck('name', 'id')
                    )
                    ->default(Auth::user()->branch_id),
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
            )
            ->defaultPaginationPageOption(25)
            ->recordAction(null) // disable clickable row to edit
            ->recordUrl(null) // disable clickable row to show
            ->defaultSort('start_date', 'desc')
            ->striped()
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->fontFamily(FontFamily::Sans)
                    ->weight(FontWeight::SemiBold)
                    ->extraAttributes([
                        'style' => 'text-transform:capitalize;',
                    ])
                    ->color(fn ($record) => $record->status == 'finished' ? 'rose' : 'primary')
                    ->url(fn ($record) => static::getUrl('view-students', ['record' => $record]))
                    ->openUrlInNewTab(false),
                Tables\Columns\TextColumn::make('students_count')
                    // students_count (students:relation name)
                    ->label('عدد الطلاب')
                    ->counts('students')
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'info' : 'warning')
                    ->formatStateUsing(fn ($state) => $state > 0 ? $state : 'لا يوجد طلاب'),
                Tables\Columns\TextColumn::make('start_date')
                    ->date('d - m - Y')
                    ->dateTooltip('M')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date('d - m - Y')
                    ->dateTooltip('M')
                    ->sortable(),
                Tables\Columns\TextColumn::make('instructor.name')
                    ->label('المحاضر'),
                Tables\Columns\TextColumn::make('branch.name')
                    ->label('الفرع'),
            ])
            ->filters([
                SelectFilter::make('instructor')
                    ->native(false)
                    ->options(
                        Instructor::when(
                            Auth::check() && Auth::user()->branch_id,
                            fn ($query) => $query->where('branch_id', Auth::user()->branch_id),
                            fn ($query) => $query // else show all groups
                        )
                            ->whereActive(true)
                            ->pluck('name', 'id')
                    )
                    ->attribute('instructor_id')
                    ->label('المحاضر'),
                SelectFilter::make('branch')
                    ->native(false)
                    ->options(
                        Branch::when(
                            Auth::check() && Auth::user()->branch_id,
                            fn ($query) => $query->where('id', Auth::user()->branch_id),
                            fn ($query) => $query // else show all groups
                        )
                            ->pluck('name', 'id')
                    )
                    ->attribute('branch_id')
                    ->label('الفرع')
                    ->hidden(fn () => Auth::check() && Auth::user()->branch_id !== null),
            ], layout: FiltersLayout::AboveContent)

            ->actions([
                Tables\Actions\EditAction::make(),
                PdfAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([]),
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
            'index' => Pages\ListTrainingGroups::route('/'),
            'view-students' => ViewStudents::route('/{record}/students'),
        ];
    }
}
