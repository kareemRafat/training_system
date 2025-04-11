<?php

namespace App\Filament\Resources\RepeatedStudentResource\Pages;

use App\Filament\Resources\RepeatedStudentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRepeatedStudent extends EditRecord
{
    protected static string $resource = RepeatedStudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
