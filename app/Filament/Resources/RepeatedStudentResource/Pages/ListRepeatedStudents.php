<?php

namespace App\Filament\Resources\RepeatedStudentResource\Pages;

use App\Filament\Resources\RepeatedStudentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRepeatedStudents extends ListRecords
{
    protected static string $resource = RepeatedStudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->createAnother(false)
                ->mutateFormDataUsing(function (array $data): array {
                    $data['created_at'] = now();

                    return $data;
                }),
        ];
    }
}
