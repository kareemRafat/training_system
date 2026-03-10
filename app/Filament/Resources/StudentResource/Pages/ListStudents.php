<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Actions\NormalActions\StudentActions\AddStudentsAction;
use App\Filament\Resources\StudentResource;
use App\Models\RepeatedStudent;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->slideOver()
                ->createAnother(false)
                ->closeModalByClickingAway(false)
                ->mutateFormDataUsing(function (array $data): array {
                    $data['created_at'] = now();

                    // Store repeated data in a temporary property to use in after()
                    // and remove them from $data so they don't get saved to the Student model
                    $this->repeatedData = [
                        'is_repeated' => $data['is_repeated'] ?? false,
                        'track_start' => $data['track_start'] ?? null,
                        'instructor_id' => $data['instructor_id'] ?? null,
                    ];

                    unset($data['is_repeated'], $data['track_start'], $data['instructor_id']);

                    return $data;
                })
                ->after(function (array $data, $record) {
                    $repData = $this->repeatedData ?? [];

                    if ($repData['is_repeated'] && $repData['track_start']) {
                        RepeatedStudent::create([
                            'name' => $record->name,
                            'phone' => $record->phone,
                            'track_start' => $repData['track_start'],
                            'repeat_status' => 'waiting',
                            'group_id' => $record->group_id,
                            'branch_id' => $record->branch_id,
                            'instructor_id' => $repData['instructor_id'],
                            'created_at' => $record->created_at ?: now(),
                        ]);
                    }
                }),

            AddStudentsAction::make('addStudents'),

        ];
    }
}
