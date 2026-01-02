<?php

namespace Adultdate\FilamentBooking\Filament\Resources\Booking\ServicePeriods\Pages;

use Adultdate\FilamentBooking\Filament\Resources\Booking\ServicePeriods\BookingServicePeriodResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBookingServicePeriods extends ListRecords
{
    protected static string $resource = BookingServicePeriodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

            protected function getHeaderWidgets(): array
    {
        return [
           \Adultdate\FilamentBooking\Filament\Resources\Booking\ServicePeriods\Widgets\BookingPeriodsCalendar::class,
        ];
    }
}
