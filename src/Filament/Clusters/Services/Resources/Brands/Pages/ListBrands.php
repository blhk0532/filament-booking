<?php

namespace Adultdate\FilamentBooking\Filament\Clusters\Services\Resources\Brands\Pages;

use Adultdate\FilamentBooking\Filament\Clusters\Services\Resources\Brands\BrandResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Adultdate\FilamentBooking\Filament\Exports\Booking\BrandExporter;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;

class ListBrands extends ListRecords
{
    protected static string $resource = BrandResource::class;

    protected function getActions(): array
    {
        return [
            ExportAction::make()
                ->exporter(BrandExporter::class),
            CreateAction::make(),
        ];
    }
}