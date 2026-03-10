<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use App\Models\RepeatedStudent;
use Filament\Resources\Pages\CreateRecord;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;

    protected function afterCreate(): void
    {
        $data = $this->form->getState();

        if ($data['is_repeated'] ?? false && $data['track_start'] ?? null) {
            RepeatedStudent::create([
                'name' => $this->record->name,
                'phone' => $this->record->phone,
                'track_start' => $data['track_start'],
                'repeat_status' => 'waiting',
                'group_id' => $this->record->group_id,
                'branch_id' => $this->record->branch_id,
                'instructor_id' => $data['instructor_id'] ?? null,
                'created_at' => $this->record->created_at ?: now(),
            ]);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
