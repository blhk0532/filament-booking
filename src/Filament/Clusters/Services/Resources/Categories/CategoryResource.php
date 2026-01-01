<?php

namespace Adultdate\FilamentShop\Filament\Clusters\Services\Resources\Categories;

use Adultdate\FilamentShop\Filament\Clusters\Services\ServicesCluster;
use Adultdate\FilamentShop\Filament\Clusters\Services\Resources\Categories\Pages\CreateCategory;
use Adultdate\FilamentShop\Filament\Clusters\Services\Resources\Categories\Pages\EditCategory;
use Adultdate\FilamentShop\Filament\Clusters\Services\Resources\Categories\Pages\ListCategories;
use Adultdate\FilamentShop\Filament\Clusters\Services\Resources\Categories\RelationManagers\ServicesRelationManager;
use Adultdate\FilamentShop\Filament\Clusters\Services\Resources\Categories\Schemas\CategoryForm;
use Adultdate\FilamentShop\Filament\Clusters\Services\Resources\Categories\Tables\CategoriesTable;
use Adultdate\FilamentShop\Models\Booking\Category;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $cluster = ServicesCluster::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationParentItem = 'Services';

    protected static ?int $navigationSort = 2;

    public static function getFormSchema(): array
    {
        return CategoryForm::configure(Schema::make())->getComponents();
    }

    public static function table(Table $table): Table
    {
        return CategoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ServicesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCategories::route('/'),
            'create' => CreateCategory::route('/create'),
            'edit' => EditCategory::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->name;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'slug'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Slug' => $record->slug,
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}