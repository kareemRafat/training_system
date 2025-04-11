<?php

namespace App\Filament\Actions\NormalActions\TrainingGroupActions;

use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class RepeatAccepted
{
    public static function make(): Action
    {
        return Action::make('printPDF')
            ->label('طباعة')
            ->icon('heroicon-o-printer')
            ->color('success')
            ->action(function (Model $record, $data): void {
                //
            });
    }
}
