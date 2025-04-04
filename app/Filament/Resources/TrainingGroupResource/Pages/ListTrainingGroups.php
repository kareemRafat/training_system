<?php

namespace App\Filament\Resources\TrainingGroupResource\Pages;

use App\Filament\Resources\TrainingGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Mail;

class ListTrainingGroups extends ListRecords
{
    protected static string $resource = TrainingGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->createAnother(false)
                // use after lifecycle hook when create with modal
                ->after(function ($record) {
                    // send email to the user
                    /* Mail::raw("new ({$record->name}) group has beend creted", function ($message) {
                        $message->to('mohamedtogo200@yahoo.com')->subject('trainig group has been created');
                    }); */
                }),
        ];
    }
}
