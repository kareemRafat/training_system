<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Branch;
use Filament\Forms\Form;
use App\Models\Instructor;
use Filament\Tables\Table;
use App\Models\TrainingGroup;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\TrainingGroupResource\Pages;
use App\Filament\Resources\TrainingGroupResource\Pages\ViewStudents;
use App\Filament\Resources\TrainingGroupResource\RelationManagers\StudentsRelationManager;

class TrainingGroupResource extends Resource
{
    protected static ?string $model = TrainingGroup::class;

    protected static ?string $navigationGroup = 'الـطـلاب';

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
                    ->required()
                    ->label('المحاضر')
                    ->options(
                        Instructor::when(
                            Auth::check() && Auth::user()->branch_id,
                            fn ($query) => $query->where('branch_id', Auth::user()->branch_id),
                            fn ($query) => $query // else show all groups
                        )
                            ->pluck('name', 'id')
                    ),
                Forms\Components\Select::make('branch_id')
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
                        'style' => 'letter-spacing: 1px;text-transform:capitalize;',
                    ])
                    ->color(fn ($record) => now()->diffInDays($record->start_date, false) <= -75 ? 'rose' : 'primary')
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
                    ->options(
                        Instructor::when(
                            Auth::check() && Auth::user()->branch_id,
                            fn ($query) => $query->where('branch_id', Auth::user()->branch_id),
                            fn ($query) => $query // else show all groups
                        )
                            ->pluck('name', 'id')
                    )
                    ->attribute('instructor_id')
                    ->label('المحاضر'),
                SelectFilter::make('branch')
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
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([

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
            'index' => Pages\ListTrainingGroups::route('/'),
            'view-students' => ViewStudents::route('/{record}/students')
        ];
    }
}
