<?php

namespace Adultdate\FilamentShop\Filament\Clusters\Products\Resources\Products\Pages;

use Adultdate\FilamentShop\Filament\Clusters\Products\Resources\Products\ProductResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
