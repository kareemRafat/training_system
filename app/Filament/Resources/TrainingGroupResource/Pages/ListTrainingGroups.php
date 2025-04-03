<?php

namespace App\Filament\Resources\TrainingGroupResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Mail;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\TrainingGroupResource;

class ListTrainingGroups extends ListRecords
{
    protected static string $resource = TrainingGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->createAnother(false)
                // after lifecycle hook when create with modal
                ->after(function () {
                    Mail::raw('Test email', function ($message) {
                        $message->to('mohamedtogo200@yahoo.com')->subject('trainig group has been created');
                    });
                }),
        ];
    }
}
