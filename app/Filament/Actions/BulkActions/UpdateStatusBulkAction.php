<?php

namespace App\Filament\Actions\BulkActions;

use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

class UpdateStatusBulkAction extends BulkAction
{
    public static function make(?string $name = 'اضف_ الى _ المستعجلين'): static
    {
        return parent::make($name)
            ->action(function (Collection $records, array $data): void {
                // $data -> form-data ---- $records -> the row need to update
                $records->each->update([
                    'status' => 'important',
                ]);
            })
            ->form([

            ])
            ->icon('heroicon-s-rocket-launch')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('تأكيد التحديث')
            ->modalDescription('هل أنت متأكد أنك تريد تغيير حالة الطلاب إلى مستعجل؟')
            ->modalSubmitActionLabel('تأكيد')
            ->modalCancelActionLabel('إلغاء')
            ->deselectRecordsAfterCompletion();

    }
}
