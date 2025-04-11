<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Actions\NormalActions\AddStudentsAction;
use App\Filament\Resources\StudentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->createAnother(false)
                ->closeModalByClickingAway(false)
                ->mutateFormDataUsing(function (array $data): array {
                    $data['created_at'] = now();

                    return $data;
                }),

            AddStudentsAction::make('addStudents'),

        ];
    }
}
