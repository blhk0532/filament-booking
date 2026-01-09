<?php

namespace Adultdate\FilamentBooking\Filament\Resources\Booking\DailyLocations\Pages;

use Adultdate\FilamentBooking\Filament\Resources\Booking\DailyLocations\DailyLocationResource;
use Adultdate\FilamentBooking\Filament\Resources\Booking\DailyLocations\Widgets\EventCalendar;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDailyLocations extends ListRecords
{
    protected static string $resource = DailyLocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            EventCalendar::class,
        ];
    }
}
