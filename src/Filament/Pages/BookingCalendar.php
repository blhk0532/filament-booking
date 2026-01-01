<?php

namespace Adultdate\FilamentBooking\Filament\Pages;

use Adultdate\FilamentBooking\Filament\Widgets\BookingCalendarWidget;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Widgets\Widget;
use UnitEnum;

class BookingCalendar extends Page
{
    protected string $view = 'filament-booking::pages.booking-calendar';

    protected static ?string $navigationLabel = 'Booking Calendar';

    protected static BackedEnum | string | null $navigationIcon = 'heroicon-o-calendar';

    protected static ?int $sort = 1;

    protected static string | UnitEnum | null $navigationGroup = 'Bookings';

    /**
     * Return header widgets for the page.
     *
     * @return array<class-string<Widget>>
     */
    protected function getHeaderWidgets(): array
    {
        return [
            BookingCalendarWidget::class,
        ];
    }
}
