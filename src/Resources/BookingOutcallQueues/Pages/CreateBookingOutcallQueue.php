<?php

namespace Adultdate\FilamentBooking\BookingOutcallQueues\Pages;

use Adultdate\FilamentBooking\BookingOutcallQueues\BookingOutcallQueueResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBookingOutcallQueue extends CreateRecord
{
    protected static string $resource = BookingOutcallQueueResource::class;
}
