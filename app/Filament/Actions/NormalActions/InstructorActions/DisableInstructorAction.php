<?php

namespace App\Filament\Actions\NormalActions\InstructorActions;

use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;

class DisableInstructorAction
{
    public static function make(): Action
    {
        return Action::make('disableInstructor')
            ->label(fn (Model $record): string => $record->active ? 'تعطيل' : 'تفعيل')
            ->color(fn (Model $record): string => $record->active ? 'danger' : 'success')
            ->icon(fn (Model $record): string => ! $record->active ? 'heroicon-s-user-minus' : 'heroicon-s-user-plus')
            ->requiresConfirmation()
            ->action(function (Model $record): void {
                // dd($record);
                $record->update(['active' => ! $record->active]);

                Notification::make()
                    ->title($record->active ? 'تم التفعيل' : 'تم التعطيل')
                    // success() or error()
                    ->{($record->active) ? 'success' : 'danger'}()
                    ->send();
            });
    }
}
