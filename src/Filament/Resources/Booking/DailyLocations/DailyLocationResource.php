<?php

namespace Adultdate\FilamentBooking\Filament\Resources\Booking\DailyLocations;

use Adultdate\FilamentBooking\Filament\Clusters\Services\ServicesCluster;

use Adultdate\FilamentBooking\Filament\Resources\Booking\DailyLocations\Pages\CreateDailyLocation;
use Adultdate\FilamentBooking\Filament\Resources\Booking\DailyLocations\Pages\EditDailyLocation;
use Adultdate\FilamentBooking\Filament\Resources\Booking\DailyLocations\Pages\ListDailyLocations;
use Adultdate\FilamentBooking\Filament\Resources\Booking\DailyLocations\Pages\ViewDailyLocation;
use Adultdate\FilamentBooking\Filament\Resources\Booking\DailyLocations\Schemas\DailyLocationForm;
use Adultdate\FilamentBooking\Filament\Resources\Booking\DailyLocations\Schemas\DailyLocationInfolist;
use Adultdate\FilamentBooking\Filament\Resources\Booking\DailyLocations\Tables\DailyLocationsTable;
use Adultdate\FilamentBooking\Models\Booking\DailyLocation;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DailyLocationResource extends Resource
{
    protected static ?string $model = DailyLocation::class;

    protected static ?string $recordTitleAttribute = 'location';

    protected static ?string $navigationLabel = 'Daily locations';

    protected static string | UnitEnum | null $navigationGroup = 'Services';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return DailyLocationForm::configure($schema);
    }

    /**
     * Force visibility for debugging; remove or tighten this before production.
     */
    public static function canViewAny(): bool
    {
        return true;
    }

    public static function infolist(Schema $schema): Schema
    {
        return DailyLocationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DailyLocationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDailyLocations::route('/'),
            'create' => CreateDailyLocation::route('/create'),
            'view' => ViewDailyLocation::route('/{record}'),
            'edit' => EditDailyLocation::route('/{record}/edit'),
        ];
    }
}
