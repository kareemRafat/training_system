<?php

namespace App\Filament\Resources;

use App\Filament\Actions\NormalActions\UserActions\DisableUserAction;
use App\Filament\Resources\UserResource\Pages;
use App\Models\Branch;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationLabel = 'خـدمـة الـعـمـلاء';

    protected static ?string $navigationGroup = 'فـريـق الـعـمـل';

    protected static ?string $modelLabel = 'موظف'; // Singular

    protected static ?string $pluralModelLabel = 'الموظفين'; // Plural

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $activeNavigationIcon = 'heroicon-s-user-circle';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('الإسم بالعربي')
                    ->required()
                    ->rule(['regex:/^[\p{Arabic}\s]+$/u', 'required'])
                    ->unique(ignoreRecord: true)
                    ->live()
                    ->afterStateUpdated(function ($livewire, $component) {
                        // live validation
                        $livewire->validateOnly($component->getStatePath());
                    }),
                Forms\Components\TextInput::make('username')
                    ->label('إسم الدخول باللغة الإنجليزية')
                    ->required()
                    ->rules('required')
                    // convert to lowercase
                    ->afterStateHydrated(fn ($component, $state) => $component->state(strtolower($state)))
                    ->dehydrateStateUsing(fn ($state) => strtolower($state))
                    ->live()
                    ->afterStateUpdated(function ($livewire, $component) {
                        // live validation
                        $livewire->validateOnly($component->getStatePath());
                    }),
                Forms\Components\TextInput::make('password')
                    ->label('الباسورد')
                    ->password()
                    ->helperText(function ($component) {
                        if ($component->getModelInstance()->exists) {
                            return 'فى حالة عدم الرغبة فى تعديل الباسورد يرجى ترك الحقل فارغاً';
                        }
                    })
                    ->required(fn ($component) => ! $component->getModelInstance()->exists)
                    ->revealable()
                    ->rules(function ($component) {
                        return $component->getModelInstance()->exists
                            ? ['nullable', 'confirmed']
                            : ['confirmed'];
                    })
                    // Prevent empty values from being sent
                    ->dehydrated(fn ($state) => filled($state)),
                Forms\Components\TextInput::make('password_confirmation')
                    ->label('تـاكـيد الـباسورد')
                    ->password()
                    ->required(fn ($component) => ! $component->getModelInstance()->exists)
                    ->revealable()
                    ->rules(function ($component) {
                        // Apply 'required_if' only during updates
                        return $component->getModelInstance()->exists
                            ? ['required_if:password,*']
                            : ['required'];
                    }),
                Forms\Components\TextInput::make('email')
                    ->label('البريد الإلكتروني'),
                Forms\Components\Select::make('branch_id')
                    ->native(false)
                    ->label('إسم الفرع')
                    ->options(
                        Branch::all()->pluck('name', 'id')
                    ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(
                // show all people except kareem
                fn (Builder $query) => $query->whereNot('username', 'kareem')
            )
            ->recordAction(null) // prevent clickable row
            ->striped()
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('الإسـم')
                    ->searchable()
                    ->fontFamily(FontFamily::Sans)
                    ->color('violet')
                    ->weight(FontWeight::Medium)
                    ->copyable()
                    ->copyMessage('تم نسخ الاسم'),
                Tables\Columns\TextColumn::make('username')
                    ->label('إسم الدخول')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('البريد الالكتروني'),
                Tables\Columns\TextColumn::make('branch.arabic_name')
                    ->label('الـفرع')
                    ->default('مسئول')
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->disabled(fn (Model $record): bool => $record->id == Auth::user()->id),
                DisableUserAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        // Show in navigation only for admins
        return Auth::check() && ! Auth::user()->branch_id;
    }

    public static function canViewAny(): bool
    {
        return Auth::check() && ! Auth::user()->branch_id;
    }
}
