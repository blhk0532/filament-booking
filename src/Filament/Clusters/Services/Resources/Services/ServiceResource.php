<?php

namespace Adultdate\FilamentShop\Filament\Clusters\Services\Resources\Services;

use Adultdate\FilamentShop\Filament\Clusters\Services\ServicesCluster;
use Adultdate\FilamentShop\Filament\Clusters\Services\Resources\Services\Pages\CreateService;
use Adultdate\FilamentShop\Filament\Clusters\Services\Resources\Services\Pages\EditService;
use Adultdate\FilamentShop\Filament\Clusters\Services\Resources\Services\Pages\ListServices;
use Adultdate\FilamentShop\Filament\Clusters\Services\Resources\Services\RelationManagers\CommentsRelationManager;
use Adultdate\FilamentShop\Filament\Clusters\Services\Resources\Services\Schemas\ServiceForm;
use Adultdate\FilamentShop\Filament\Clusters\Services\Resources\Services\Tables\ServicesTable;
use Adultdate\FilamentShop\Filament\Clusters\Services\Resources\Services\Widgets\ServiceStats;
use Adultdate\FilamentShop\Models\Booking\Service;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $cluster = ServicesCluster::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationLabel = 'Services';

    protected static ?int $navigationSort = 0;

    public static function form(Schema $schema): Schema
    {
        return ServiceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ServicesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            CommentsRelationManager::class,
        ];
    }

    public static function getWidgets(): array
    {
        return [
            ServiceStats::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListServices::route('/'),
            'create' => CreateService::route('/create'),
            'edit' => EditService::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->name;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'service_code', 'description'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Brand' => $record->brand?->name,
            'Price' => $record->price ? '$' . number_format($record->price, 2) : null,
            'Status' => $record->status->getLabel(),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}