<?php

namespace Adultdate\FilamentShop\Filament\Clusters\Services\Resources\Categories\Pages;

use Adultdate\FilamentShop\Filament\Clusters\Services\Resources\Categories\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;
}