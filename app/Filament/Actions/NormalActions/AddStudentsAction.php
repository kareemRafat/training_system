<?php

namespace App\Filament\Actions\NormalActions;

use App\Filament\Resources\StudentResource\Pages\AddStudents;
use Filament\Actions\Action;

class AddStudentsAction extends Action
{
    public static function make(?string $name = null): static // ✅ Allow null
    {
        return parent::make($name)
            ->label('إضـافـة طـلاب') // Button label
            ->icon('heroicon-o-user-plus') // Optional icon

            // get the url of add students class in pages folder
            ->url(fn ($record) => AddStudents::getUrl());
    }
}
