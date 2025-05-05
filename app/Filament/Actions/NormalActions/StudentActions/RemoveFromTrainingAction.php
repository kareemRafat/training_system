<?php

namespace App\Filament\Actions\NormalActions\StudentActions;

use App\Traits\AddActivityLogs;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;

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
            ->visible(fn(Model $record) => ! is_null($record->training_group_id))
            ->action(fn(Model $record) => $this->remove($record));
    }

    public function remove(Model $record): void
    {
        DB::transaction(function () use ($record) {
            $record->update([
                'training_group_id' => null,
                'training_joined_at' => null,
                'received_certificate' => false,
                'has_certificate' => false,
            ]);

            // ✅ add update training group to activity logs
            AddActivityLogs::Add(
                event: 'training_group',
                action: 'إزالة من جروب التدريب',
                value: 'تمت الإزالة من جروب التدريب',
                record: $record
            );
        });

        // ✅ Show success notification
        Notification::make()
            ->title('تم ازالة الطالب من مجموعة التدريب')
            ->success()
            ->send();
    }
}
