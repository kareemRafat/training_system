<?php

namespace App\Filament\Actions\BulkActions;

use App\Traits\AddActivityLogs;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

class RecievedCertificateBulkAction extends BulkAction
{
    use AddActivityLogs;

    public static function make(?string $name = 'طباعة_شهادة_التدريب'): static
    {
        return parent::make($name)
            ->action(function (Collection $records, array $data): void {
                // $data -> form-data ---- $records -> the row need to update
                $records->each(function ($record) {
                    if ($record->training_group !== null) {
                        $record->update([
                            'has_certificate' => true,
                        ]);

                        // ✅ Add the comment to the activity logs
                        AddActivityLogs::Add(
                            event: 'certificate',
                            action: 'طباعة الشهادة',
                            value: 'تم طباعة الشهادة',
                            record: $record
                        );

                        // ✅ Show success notification
                        Notification::make()
                            ->title("تم إضافة  {$record->name} الى طباعة الشهادات")
                            ->success()
                            ->seconds(10)
                            ->color('success')
                            ->send();
                    } else {
                        Notification::make()
                            ->title("لا يمكن طباعة شهادة للطالب {$record->name}")
                            ->body('يجب أن يكون الطالب في مجموعة تدريبية')
                            ->warning()
                            ->seconds(10)
                            ->color('warning')
                            ->send();
                    }
                });
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
