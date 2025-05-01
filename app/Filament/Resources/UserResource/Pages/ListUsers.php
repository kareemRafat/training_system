<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->slideOver()
                ->createAnother(false)
                ->mutateFormDataUsing(function (array $data): array {
                    $data['created_at'] = now();

                    return $data;
                }),
            // ->visible(fn (): bool => Auth::user()->username == 'kareem'),
        ];
    }
}
