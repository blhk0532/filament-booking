<?php

namespace Adultdate\FilamentBooking\Filament\Widgets;

use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Widgets\Widget;
use Adultdate\FilamentBooking\Concerns\InteractsWithCalendar;
use Adultdate\FilamentBooking\Contracts\HasCalendar;
// Use fully-qualified class to avoid static analysis issues with the facade import.

abstract class SimpleCalendarWidget extends Widget implements HasActions, HasCalendar, HasSchemas
{
    use InteractsWithCalendar;


    protected string $view = 'adultdate/filament-booking::calendar-widget';

    protected int | string | array $columnSpan = 'full';

    public function eventAssetUrl(): string
    {
        return \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('calendar-event', 'adultdate/filament-booking');
    }
}
