<?php

namespace Adultdate\FilamentBooking\Filament\Clusters\Services\Resources\Brands\Pages;

use Adultdate\FilamentBooking\Filament\Clusters\Services\Resources\Brands\BrandResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBrand extends CreateRecord
{
    protected static string $resource = BrandResource::class;
}