<?php

namespace Adultdate\FilamentBooking\Contracts;

use Adultdate\FilamentBooking\ValueObjects\CalendarEvent;

interface Eventable
{
    public function toCalendarEvent(): CalendarEvent;
}
