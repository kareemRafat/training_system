<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GroupResource\Pages;
use App\Models\Branch;
use App\Models\Group;
use App\Models\Instructor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class GroupResource extends Resource
{
    protected static ?string $model = Group::class;

    // protected static ?string $navigationGroup = 'التدريب';

    protected static ?string $navigationLabel = 'جـروبـات الـكـورس الـمـضـافـة';

    protected static ?string $modelLabel = 'المجموعة'; // Singular

    protected static ?string $pluralModelLabel = 'المجموعات'; // Plural

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $activeNavigationIcon = 'heroicon-s-academic-cap';

    protected static ?int $navigationSort = 1; // Position in sidebar

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
                        // live validation
                        $livewire->validateOnly($component->getStatePath());
                    }),
                Forms\Components\DatePicker::make('start_date')
                    ->required()
                    ->label('تاريخ البدء'),
                Forms\Components\DatePicker::make('end_date')
                    ->required()
                    ->label('تاريخ الانتهاء')
                    ->default(now()),
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
                    ->native(false)
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
            ->recordAction(null)
            ->defaultSort('end_date', 'desc')
            ->striped()
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->weight(FontWeight::SemiBold)
                    ->extraAttributes([
                        'style' => 'text-transform:capitalize;',
                    ])
                    ->color('info'),
                Tables\Columns\TextColumn::make('start_date')
                    ->date('d - m - Y')
                    ->dateTooltip('M')
                    ->sortable()
                    ->label('تاريخ البدء'),
                Tables\Columns\TextColumn::make('end_date')
                    ->date('d - m - Y')
                    ->dateTooltip('M')
                    ->sortable()
                    ->label('تاريخ الانتهاء'),
                Tables\Columns\TextColumn::make('instructor.name')
                    ->label('المحاضر'),
                Tables\Columns\TextColumn::make('branch.name')
                    ->label('الفرع'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListGroups::route('/'),
        ];
    }
}
