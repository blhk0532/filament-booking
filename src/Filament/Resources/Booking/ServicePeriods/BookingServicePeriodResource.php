<?php

namespace Adultdate\FilamentBooking\Filament\Resources\Booking\ServicePeriods;

use Adultdate\FilamentBooking\Filament\Resources\Booking\ServicePeriods\Pages\CreateBookingServicePeriod;
use Adultdate\FilamentBooking\Filament\Resources\Booking\ServicePeriods\Pages\EditBookingServicePeriod;
use Adultdate\FilamentBooking\Filament\Resources\Booking\ServicePeriods\Pages\ListBookingServicePeriods;
use Adultdate\FilamentBooking\Filament\Resources\Booking\ServicePeriods\Pages\ViewBookingServicePeriod;
use Adultdate\FilamentBooking\Filament\Resources\Booking\ServicePeriods\Schemas\BookingServicePeriodForm;
use Adultdate\FilamentBooking\Filament\Resources\Booking\ServicePeriods\Schemas\BookingServicePeriodInfolist;
use Adultdate\FilamentBooking\Filament\Resources\Booking\ServicePeriods\Tables\BookingServicePeriodsTable;
use Adultdate\FilamentBooking\Models\BookingServicePeriod;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BookingServicePeriodResource extends Resource
{
    protected static ?string $model = BookingServicePeriod::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return BookingServicePeriodForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BookingServicePeriodInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BookingServicePeriodsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getWidgets(): array
    {
     
            return [\Adultdate\FilamentBooking\Filament\Resources\Booking\ServicePeriods\Widgets\BookingPeriodsCalendar::class];
      
    }


    public static function getPages(): array
    {
        return [
            'index' => ListBookingServicePeriods::route('/'),
            'create' => CreateBookingServicePeriod::route('/create'),
            'view' => ViewBookingServicePeriod::route('/{record}'),
            'edit' => EditBookingServicePeriod::route('/{record}/edit'),
        ];
    }
}
