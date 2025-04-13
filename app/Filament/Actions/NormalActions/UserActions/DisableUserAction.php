<?php

namespace App\Filament\Actions\NormalActions\UserActions;

use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class DisableUserAction
{
    public static function make(): Action
    {
        return Action::make('disableUser')
            ->label(fn (Model $record): string => $record->is_active === 'active' ? 'تعطيل' : 'تفعيل')
            ->color(fn (Model $record): string => $record->is_active === 'active' ? 'danger' : 'success')
            ->icon(fn (Model $record): string => $record->id == Auth::user()->id ? 'heroicon-s-lock-closed' : ($record->is_active === 'active' ? 'heroicon-s-user-minus' : 'heroicon-s-user-plus'))
            ->requiresConfirmation()
            ->action(function (Model $record): void {
                $record->update(['is_active' => $record->is_active === 'active' ? 'banned' : 'active']);

                Notification::make()
                    ->title($record->is_active === 'active' ? 'تم التفعيل' : 'تم التعطيل')
                    // success() or error()
                    ->{($record->is_active == 'active') ? 'success' : 'danger'}()
                    ->send();
            })
            ->disabled(fn (Model $record): bool => $record->id == Auth::user()->id);
    }
}
