<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BranchResource\Pages;
use App\Models\Branch;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;

class BranchResource extends Resource
{
    protected static ?string $model = Branch::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationLabel = 'الـفـروع';

    protected static ?string $navigationGroup = 'فـريـق الـعـمـل';

    protected static ?string $modelLabel = 'فرع'; // Singular

    protected static ?string $pluralModelLabel = 'الـفـروع'; // Plural

    protected static ?string $activeNavigationIcon = 'heroicon-s-map';

    protected static ?int $navigationSort = 7; // Position in sidebar

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('اسم الفرع')
                    ->required()
                    ->label('اسم الفرع')
                    ->unique(ignoreRecord: true)
                    ->validationMessages([
                        'unique' => 'الإسم مسجل مسبقاً',
                    ])
                    ->live()
                    ->afterStateUpdated(function ($livewire, $component) {
                        // live validation
                        $livewire->validateOnly($component->getStatePath());
                    }),
                Forms\Components\TextInput::make('arabic_name')
                    ->label('الإسم بالعربي')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->validationMessages([
                        'unique' => 'الإسم مسجل مسبقاً',
                    ])
                    ->live()
                    ->afterStateUpdated(function ($livewire, $component) {
                        // live validation
                        $livewire->validateOnly($component->getStatePath());
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordAction(null)
            ->striped()
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('اسم الفرع')
                    ->searchable(),
                Tables\Columns\TextColumn::make('arabic_name')
                    ->label('الإسم بالعربي')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListBranches::route('/'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        // Show in navigation only for admins
        return Auth::check() && ! Auth::user()->branch_id;
    }

    public static function canViewAny(): bool
    {

        if (! Auth::check() || Auth::user()->branch_id) {
            throw new AuthorizationException("You don't have permission to view this.");
        }

        return true;
    }
}
