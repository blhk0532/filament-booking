<?php

namespace Adultdate\FilamentShop\Filament\Clusters\Products\Resources\Categories\Pages;

use Adultdate\FilamentShop\Filament\Clusters\Products\Resources\Categories\CategoryResource;
use Adultdate\FilamentShop\Filament\Imports\Shop\CategoryImporter;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getActions(): array
    {
        return [
            ImportAction::make()
                ->importer(CategoryImporter::class),
            CreateAction::make(),
        ];
    }
}
