<?php

namespace App\Filament\Actions\BulkActions;

use App\Models\trainingGroup;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class UpdateTrainingGroupBulkAction extends BulkAction
{
    public static function make(?string $name = 'اضف_ الى _ جروب _ التدريب'): static
    {
        return parent::make($name)
            ->action(function (Collection $records, array $data): void {
                // $data -> form-data ---- $records -> the row need to update
                $records->each->update([
                    'training_group_id' => $data['training_group_id'],
                ]);

                Notification::make()
                    ->title('تم أضافة الطلاب الى مجموعة التدريب')
                    ->success()
                    ->send();
            })
            ->form([
                Select::make('training_group_id')
                    ->label('اختر جروب التدريب')
                    ->options(
                        TrainingGroup::orderBy('start_date', 'desc')
                            ->where('status', 'active')
                            ->when(
                                Auth::check() && Auth::user()->branch_id,
                                fn ($query) => $query->where('branch_id', Auth::user()->branch_id),
                                fn ($query) => $query // else show all groups
                            )
                            ->pluck('name', 'id')
                    )
                    ->required(),
            ])
            ->icon('heroicon-s-academic-cap')
            ->color('warning')
            ->modalHeading('إضافة الطلاب الي جروب التدريب')
            ->modalSubmitActionLabel('تحديث')
            ->deselectRecordsAfterCompletion();
    }
}
