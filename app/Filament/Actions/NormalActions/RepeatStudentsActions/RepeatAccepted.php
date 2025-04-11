<?php

namespace App\Filament\Actions\NormalActions\RepeatStudentsActions;

use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class RepeatAccepted
{
    public static function make(): Action
    {
        return Action::make('acceptRepeatedStudent')
            ->label(fn(Model $record): string => $record->repeat_status === 'accepted' ? 'طلب  إعادة' : 'أضافة الى جروب')
            ->color(fn(Model $record): string => $record->repeat_status === 'accepted' ? 'danger' : 'success')
            ->icon(fn(Model $record): string => $record->repeat_status === 'accepted' ? 'heroicon-s-user-minus' : 'heroicon-s-user-plus')
            ->requiresConfirmation()
            ->form([
                Textarea::make('comment')
                    ->label('تعليق')
                    ->required(),
            ])
            ->action(function (Model $record, $data): void {
                $record->update(['repeat_status' => $record->repeat_status === 'accepted' ? 'waiting' : 'accepted']);
                $record->comments()->create([
                    'comment' => $data['comment'],
                    'user_id' => Auth::id(),
                    'created_at' => now()->setTimezone(config('app.timezone'))->toDateTimeString(),
                ]);

                Notification::make()
                    ->title($record->repeat_status === 'accepted' ? 'تم اضافته الى جروب للإعادة' : 'تم التأجيل')
                    // success() or error()
                    ->{($record->repeat_status == 'accepted') ? 'success' : 'danger'}()
                    ->send();
            });
    }
}
