<?php

namespace Adultdate\FilamentBooking\Filament\Widgets;


use Filament\Schemas\Contracts\HasSchemas;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Widgets\Widget;
use Adultdate\FilamentBooking\Concerns\InteractsWithCalendar;
use Adultdate\FilamentBooking\Contracts\HasCalendar;
use Filament\Forms\Contracts\HasForms;// Use fully-qualified class to avoid static analysis issues with the facade import.
use Adultdate\FilamentBooking\FilamentBookingPlugin;
use Adultdate\FilamentBooking\Concerns\HasHeaderActions;
use Adultdate\FilamentBooking\Filament\Widgets\Concerns\CanBeConfigured;
use Adultdate\FilamentBooking\Filament\Widgets\Concerns\InteractsWithRawJS;
abstract class SimpleCalendarWidget extends Widget implements  HasForms, HasActions
{
     use InteractsWithForms;
    use InteractsWithActions;
    use HasHeaderActions, CanBeConfigured, InteractsWithRawJS;

   // protected string $view = 'adultdate/filament-booking::service-periods-fullcalendar';
    protected string $view = 'adultdate/filament-booking::calendar-widget';

//    protected int | string | array $columnSpan = 'full';

    public function eventAssetUrl(): string
    {
        return \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('calendar-event', 'adultdate/filament-booking');
    }
}
