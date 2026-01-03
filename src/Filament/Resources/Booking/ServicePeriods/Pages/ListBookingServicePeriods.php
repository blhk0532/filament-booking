<?php

namespace Adultdate\FilamentBooking\Filament\Resources\Booking\ServicePeriods\Pages;

use Adultdate\FilamentBooking\Filament\Resources\Booking\ServicePeriods\BookingServicePeriodResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;

class ListBookingServicePeriods extends ListRecords
{
    protected static string $resource = BookingServicePeriodResource::class;

    protected function getHeaderActions(): array
    {
        return [
               Select::make('booking_client_id')
                ->label('Service User')
                ->options([1,2,3,4,5])
                ->searchable()
                ->preload()
        ];
    }

            protected function getHeaderWidgets(): array
    {
        return [
           \Adultdate\FilamentBooking\Filament\Resources\Booking\ServicePeriods\Widgets\BookingPeriodsCalendar::class,
        ];
    }
}
