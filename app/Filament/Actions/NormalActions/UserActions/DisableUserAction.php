<?php

namespace App\Filament\Actions\NormalActions\UserActions;

use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;

class DisableUserAction
{
    public static function make(): Action
    {
        return Action::make('disableUser')
            ->label('تعطيل')
            ->icon('heroicon-o-user-minus')
            ->color('danger')
            ->requiresConfirmation()
            ->action(function (Model $record): void {
                $record->update(['is_active' => $record->is_active === 'active' ? 'banned' : 'active']);

                Notification::make()
                    ->title($record->is_active === 'active' ? 'تم التفعيل' : 'تم التعطيل')
                    // success() or error()
                    ->{($record->is_active == 'active') ? 'success' : 'danger'}()
                    ->send();
            })
            ->label(fn(Model $record): string => $record->is_active === 'active' ? 'تعطيل' : 'تفعيل')
            ->color(fn(Model $record): string => $record->is_active === 'active' ? 'danger' : 'success')
            ->disabled(fn(Model $record): bool => $record->id == Auth::user()->id)
            ->icon(fn(Model $record): string => $record->id == Auth::user()->id ? 'heroicon-s-lock-closed' : ($record->is_active === 'active' ? 'heroicon-s-user-minus' : 'heroicon-s-user-plus'));
    }
}
