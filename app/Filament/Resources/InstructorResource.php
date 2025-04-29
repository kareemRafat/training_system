<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Branch;
use Filament\Forms\Form;
use App\Models\Instructor;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Auth\Access\AuthorizationException;
use App\Filament\Resources\InstructorResource\Pages;
use App\Filament\Actions\NormalActions\InstructorActions\DisableInstructorAction;

class InstructorResource extends Resource
{
    protected static ?string $model = Instructor::class;

    protected static ?string $navigationLabel = 'الـمـحـاضـريـن';

    protected static ?string $navigationGroup = 'فـريـق الـعـمـل';

    protected static ?string $modelLabel = 'المحاضر'; // Singular

    protected static ?string $pluralModelLabel = 'المحاضرين'; // Plural

    protected static ?string $navigationIcon = 'heroicon-o-command-line';

    protected static ?string $activeNavigationIcon = 'heroicon-s-command-line';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('اسم المحاضر')
                    ->unique(ignoreRecord: true)
                    ->validationMessages([
                        'unique' => 'الإسم مسجل مسبقاً',
                    ])
                    ->live()
                    ->afterStateUpdated(function ($livewire, $component) {
                        // live validation
                        $livewire->validateOnly($component->getStatePath());
                    }),
                Forms\Components\Select::make('branch_id')
                    ->native(false)
                    ->required()
                    ->label('الفرع')
                    ->options(Branch::all()->pluck('name', 'id')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // show only the branch stuff to the employee
            ->modifyQueryUsing(
                fn(Builder $query) => $query
                    ->when(Auth::check() && Auth::user()->branch_id, function (Builder $query) {
                        $query->where('branch_id', Auth::user()->branch_id);
                    })
            )
            ->defaultPaginationPageOption(25)
            ->recordAction(null)
            ->striped()
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('اسم المحاضر')
                    ->fontFamily(FontFamily::Sans)
                    ->weight(FontWeight::Medium)
                    ->color('violet')
                    ->searchable(),
                Tables\Columns\TextColumn::make('branch.name')
                    ->label('الفرع'),
                Tables\Columns\IconColumn::make('active')
                    ->label('الحالة')
                    ->boolean()
                    ->trueIcon('heroicon-s-check-circle')
                    ->trueColor('success')
                    ->falseIcon('heroicon-s-x-circle')
                    ->falseColor('danger'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                DisableInstructorAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListInstructors::route('/'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        // Show in navigation only for admins
        return Auth::check() && ! Auth::user()->branch_id;
    }

    public static function canViewAny(): bool
    {

        if (!Auth::check() || Auth::user()->branch_id) {
            throw new AuthorizationException("You don't have permission to view this.");
        }
        return  true;
    }
}
