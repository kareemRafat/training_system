<?php

namespace App\Filament\Actions\NormalActions\StudentActions;

use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;

class UpdateStatusAction extends Action
{
    protected function setUp(): void
    {
        $this
            ->label('تغيير الحالة الى مستعجل')
            ->disabled(fn (Model $record): bool => $record->status === 'important')
            ->action(function (Model $record): void {
                // $data -> form-data ---- $record -> the row need to update
                $record->update([
                    'status' => 'important',
                ]);

                // ✅ Show success notification
                Notification::make()
                    ->title('تم تغيير الحالة الى متسعجل للطالب : '.$record->name)
                    ->success()
                    ->send();
            })
            ->requiresConfirmation()
            ->color('danger')
            ->modalHeading(fn (Model $record): string => 'تغيير الحالة الى مستعجل للطالب : '.$record->name)
            ->modalSubmitActionLabel('تأكيد')
            ->icon('heroicon-s-rocket-launch')
            ->modalSubmitActionLabel('تحديث')
            ->deselectRecordsAfterCompletion();
    }
}
