<?php

namespace App\Filament\Resources\OldStudentResource\Pages;

use App\Filament\Resources\OldStudentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOldStudents extends ListRecords
{
    protected static string $resource = OldStudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
