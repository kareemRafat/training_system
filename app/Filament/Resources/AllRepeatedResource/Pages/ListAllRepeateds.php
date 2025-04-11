<?php

namespace App\Filament\Resources\AllRepeatedResource\Pages;

use App\Filament\Resources\AllRepeatedResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAllRepeateds extends ListRecords
{
    protected static string $resource = AllRepeatedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
