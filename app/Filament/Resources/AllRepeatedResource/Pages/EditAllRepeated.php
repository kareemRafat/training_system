<?php

namespace App\Filament\Resources\AllRepeatedResource\Pages;

use App\Filament\Resources\AllRepeatedResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAllRepeated extends EditRecord
{
    protected static string $resource = AllRepeatedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
