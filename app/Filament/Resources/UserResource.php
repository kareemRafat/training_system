<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Branch;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;

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
                    ->label('الإسم')
                    ->required()
                    ->rules('required')
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('username')
                    ->label('إسم الدخول')
                    ->required()
                    ->rules('required'),
                Forms\Components\TextInput::make('password')
                    ->label('الباسورد')
                    ->password()
                    ->helperText(function($component) {
                        if ($component->getModelInstance()->exists) {
                            return 'فى حالة عدم الرغبة فى تعديل الباسورد يرجى ترك الحقل فارغاً';
                        }
                    })
                    ->required(fn($component) => !$component->getModelInstance()->exists)
                    ->revealable()
                    ->rules(function ($component) {
                        return $component->getModelInstance()->exists
                            ? ['nullable', 'confirmed']
                            : ['confirmed'];
                    })
                    ->validationMessages([
                        'required' => 'الباسورد مطلوبة',
                        'confirmed' => 'يجب ان تكون كلمة المرور وتأكيدها متطابقتين',
                    ])
                    // Prevent empty values from being sent
                    ->dehydrated(fn($state) => filled($state)),
                Forms\Components\TextInput::make('password_confirmation')
                    ->label('تـاكـيد الـباسورد')
                    ->password()
                    ->required(fn($component) => !$component->getModelInstance()->exists)
                    ->revealable()
                    ->rules(function ($component) {
                        // Apply 'required_if' only during updates
                        return $component->getModelInstance()->exists
                            ? ['required_if:password,*']
                            : ['required'];
                    })
                    ->validationMessages([
                        'required_if' => 'تأكيد الباسورد مطلوب عند إدخال كلمة مرور',
                    ]),
                Forms\Components\TextInput::make('email')
                    ->label('البريد الإلكتروني')
                    ->required()
                    ->rules(['email', 'required']),
                // ->email()
                Forms\Components\Select::make('branch_id')
                    ->label('إسم الفرع')
                    ->required()
                    ->rules('required')
                    ->options(
                        Branch::all()->pluck('name', 'id')
                    ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('الإسـم')
                    ->searchable()
                    ->fontFamily(FontFamily::Sans)
                    ->extraAttributes([
                        'style' => 'letter-spacing: 1px;',
                    ])
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
                Tables\Actions\EditAction::make(),
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
