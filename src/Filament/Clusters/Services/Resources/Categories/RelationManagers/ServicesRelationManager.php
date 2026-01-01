<?php

namespace Adultdate\FilamentShop\Filament\Clusters\Services\Resources\Categories\RelationManagers;

use Adultdate\FilamentShop\Filament\Clusters\Services\Resources\Services\ServiceResource;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ServicesRelationManager extends RelationManager
{
    protected static string $relationship = 'services';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Schema $schema): Schema
    {
        return ServiceResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return ServiceResource::table($table)
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                DeleteAction::make(),
            ])
            ->groupedBulkActions([
                DeleteBulkAction::make(),
            ]);
    }
}