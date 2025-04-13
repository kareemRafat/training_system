<?php

namespace App\Filament\Actions\NormalActions;

use App\Models\TrainingGroup;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class UpdateTrainingGroupAction extends Action
{
    protected function setUp(): void
    {
        $this
            // ->name('UpdateTrainingGroup')
            ->label('أضف الى جروب التدريب')
            ->color('warning')
            ->modalHeading('اضف الى جروب التدريب')
            ->modalSubmitActionLabel('تأكيد')
            // ✅ Show only if training_group_id is NOT NULL
            ->visible(fn (Model $record) => is_null($record->training_group_id))
            ->action(fn (Model $record, $data) => $record->update([
                'training_group_id' => $data['training_group_id'],
                'training_joined_at' => now(config('app.timezone'))->toDateTimeString(),
            ]))
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
                            ->limit(5)
                            ->pluck('name', 'id')
                    )
                    ->required()
                    ->searchable()
                    ->noSearchResultsMessage('لا يوجد مجموعات تدريب'),
            ])
            ->icon('heroicon-s-academic-cap')
            ->color('warning')
            ->modalHeading('إضافة الطلاب الي جروب التدريب')
            ->modalSubmitActionLabel('تحديث')
            ->deselectRecordsAfterCompletion();
    }

    public function update(Model $record, $data): void
    {
        $record->update([
            'training_group_id' => $data['training_group_id'],
            'updated_at' => now(),
        ]);

        // ✅ Show success notification
        Notification::make()
            ->title('تم أضافة الطالب الى مجموعة التدريب')
            ->success()
            ->send();
    }
}
