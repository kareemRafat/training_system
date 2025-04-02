<?php

namespace App\Filament\Resources\TrainingGroupResource\Pages;

use App\Filament\Resources\TrainingGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTrainingGroups extends ListRecords
{
    protected static string $resource = TrainingGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->createAnother(false),
        ];
    }
}
