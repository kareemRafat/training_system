<?php

namespace App\Filament\Actions\BulkActions;

use Filament\Notifications\Notification;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

class RecievedCertificateBulkAction extends BulkAction
{
    public static function make(?string $name = 'طباعة_شهادة_التدريب'): static
    {
        return parent::make($name)
            ->action(function (Collection $records, array $data): void {
                // $data -> form-data ---- $records -> the row need to update
                $records->each->update([
                    'has_certificate' => true,
                ]);

                // ✅ Show success notification
                Notification::make()
                    ->title('تم إضافة الى طباعة الشهادات')
                    ->success()
                    ->send();
            })
            ->icon('heroicon-s-academic-cap')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('تأكيد طباعة الشهادات')
            ->modalDescription('هل أنت متأكد أنك تريد طباعة شهادات التدريب للطلاب المحددين؟')
            ->modalSubmitActionLabel('تأكيد')
            ->deselectRecordsAfterCompletion();
    }
}
