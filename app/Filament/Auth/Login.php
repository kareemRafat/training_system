<?php

namespace App\Filament\Auth;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Login as BaseAuth;
use Illuminate\Validation\ValidationException;

class Login extends BaseAuth
{

    // override getForm()
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getLoginFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getRememberFormComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }


    // override getLoginFormComponent()
    protected function getLoginFormComponent(): Component
    {
        return TextInput::make('login')
            ->label('الإسـم')
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }


    // override getCredentialsFromFormData()
    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'username' => $data['login'],
            'password'  => $data['password'],
        ];
    }

    // override throwFailureValidationException()
    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.login' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }
}
