<?php

namespace Adultdate\FilamentBooking\Filament\Clusters\Services\Resources\Clients\Pages;

use Adultdate\FilamentBooking\Filament\Clusters\Services\Resources\Clients\ClientResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListClients extends ListRecords
{
    protected static string $resource = ClientResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}