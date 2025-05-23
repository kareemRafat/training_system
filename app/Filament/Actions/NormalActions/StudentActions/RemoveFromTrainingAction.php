<?php

namespace App\Filament\Actions\NormalActions\StudentActions;

use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;

class RemoveFromTrainingAction extends Action
{
    protected function setUp(): void
    {
        $this
            // ->name('removeFromTraining')
            ->label('تعيين كغير متدرب')
            ->color('danger')
            ->icon('heroicon-s-x-circle')
            ->requiresConfirmation()
            ->modalHeading('إلغاء التدريب')
            ->modalDescription('هل أنت متأكد أنك تريد إلغاء التدريب لهذا الطالب؟')
            ->modalSubmitActionLabel('تأكيد')
            // ✅ Show only if training_group_id is NOT NULL
            ->visible(fn (Model $record) => ! is_null($record->training_group_id))
            ->action(fn (Model $record) => $this->remove($record));
    }

    public function remove(Model $record): void
    {
        $record->update([
            'training_group_id' => null,
            'training_joined_at' => null,
            'received_certificate' => false,
            'has_certificate' => false,
        ]);

        // ✅ Show success notification
        Notification::make()
            ->title('تم ازالة الطالب من مجموعة التدريب')
            ->success()
            ->send();
    }
}
