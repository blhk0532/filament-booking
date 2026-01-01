<?php

namespace Adultdate\FilamentBooking\Filament\Clusters\Services\Resources\Brands\Pages;

use Adultdate\FilamentBooking\Filament\Clusters\Services\Resources\Brands\BrandResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBrands extends ListRecords
{
    protected static string $resource = BrandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}