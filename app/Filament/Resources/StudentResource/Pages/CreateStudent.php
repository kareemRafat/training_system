<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use App\Models\RepeatedStudent;
use Filament\Resources\Pages\CreateRecord;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Store repeated data in a temporary property to use in afterCreate
        $this->repeatedData = [
            'is_repeated' => $data['is_repeated'] ?? false,
            'track_start' => $data['track_start'] ?? null,
            'instructor_id' => $data['instructor_id'] ?? null,
        ];

        // Remove from $data so it doesn't try to save to Student model
        unset($data['is_repeated'], $data['track_start'], $data['instructor_id']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $repData = $this->repeatedData ?? [];

        if ($repData['is_repeated'] && $repData['track_start']) {
            RepeatedStudent::create([
                'name' => $this->record->name,
                'phone' => $this->record->phone,
                'track_start' => $repData['track_start'],
                'repeat_status' => 'waiting',
                'group_id' => $this->record->group_id,
                'branch_id' => $this->record->branch_id,
                'instructor_id' => $repData['instructor_id'],
                'created_at' => $this->record->created_at ?: now(),
            ]);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
