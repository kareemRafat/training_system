<?php

namespace App\Filament\Resources\TrainingGroupResource\Pages;

use App\Filament\Resources\TrainingGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTrainingGroup extends EditRecord
{
    protected static string $resource = TrainingGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
