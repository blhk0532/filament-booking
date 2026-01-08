<?php

namespace Adultdate\FilamentBooking\Filament\Resources\BookingCalendars;



use Adultdate\FilamentBooking\Filament\Resources\BookingCalendars\Schemas\BookingCalendarForm;
use Adultdate\FilamentBooking\Filament\Resources\BookingCalendars\Tables\BookingCalendarsTable;
use Adultdate\FilamentBooking\Models\BookingCalendar;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

use Adultdate\FilamentBooking\Filament\Resources\BookingCalendars\Pages\ListBookingCalendars;
use Adultdate\FilamentBooking\Filament\Resources\BookingCalendars\Pages\CreateBookingCalendar;
use Adultdate\FilamentBooking\Filament\Resources\BookingCalendars\Pages\EditBookingCalendar;



class BookingCalendarResource extends Resource
{
    protected static ?string $model = BookingCalendar::class;

    protected static ?string $navigationLabel = 'Kalender';

    protected static bool $isScopedToTenant = false;

    protected static string|UnitEnum|null $navigationGroup = 'Kalender';

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?int $sort = 8;

    protected static ?int $navigationSort = 8;

    public static function form(Schema $schema): Schema
    {
        return BookingCalendarForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BookingCalendarsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBookingCalendars::route('/'),
            'create' => CreateBookingCalendar::route('/create'),
            'edit' => EditBookingCalendar::route('/{record}/edit'),
        ];
    }
}
