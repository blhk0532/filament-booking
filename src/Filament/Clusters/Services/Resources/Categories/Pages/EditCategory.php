<?php

namespace Adultdate\FilamentShop\Filament\Clusters\Services\Resources\Categories\Pages;

use Adultdate\FilamentShop\Filament\Clusters\Services\Resources\Categories\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}