<?php

namespace Adultdate\FilamentBooking\Filament\Resources\Booking\DailyLocations\Widgets;

use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Widgets\Widget;
use Adultdate\FilamentBooking\Concerns\InteractsWithCalendar;
use Adultdate\FilamentBooking\Contracts\HasCalendar;
use Adultdate\FilamentBooking\ValueObjects\FetchInfo;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;

class LocationCalendarWidget extends Widget implements HasCalendar, HasActions, HasSchemas
{
    use InteractsWithCalendar;
    use InteractsWithActions;
    use InteractsWithSchemas;

    protected string $view = 'adultdate/filament-booking::calendar-widget';

    protected int | string | array $columnSpan = 'full';

    public function eventAssetUrl(): string
    {
        return \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('calendar-event', 'adultdate/filament-booking');
    }

    protected function getEvents(FetchInfo $info): array
    {
        // Implement logic to return an array of calendar events
        return [];
    }

        public function getView(): string
    {
        return 'adultdate/filament-booking::calendar-widget';
    }

}
