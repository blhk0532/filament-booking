<?php

namespace Adultdate\FilamentBooking\Contracts;

use Adultdate\FilamentBooking\ValueObjects\CalendarResource;

interface Resourceable
{
    public function toCalendarResource(): CalendarResource;
}
