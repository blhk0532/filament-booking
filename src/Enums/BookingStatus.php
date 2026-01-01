<?php

namespace Adultdate\FilamentBooking\Enums;

use Filament\Support\Colors\Color;
use Filament\Tables\Columns\Enums\BadgeColor;

enum BookingStatus: string
{
    case New = 'new';
    case Booked = 'booked';
    case Confirmed = 'confirmed';
    case Processing = 'processing';
    case Cancelled = 'cancelled';
    case Updated = 'updated';
    case Complete = 'complete';

    public function getLabel(): string
    {
        return match ($this) {
            self::New => 'New',
            self::Booked => 'Booked',
            self::Confirmed => 'Confirmed',
            self::Processing => 'Processing',
            self::Cancelled => 'Cancelled',
            self::Updated => 'Updated',
            self::Complete => 'Complete',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::New => 'gray',
            self::Booked => 'primary',
            self::Confirmed => 'warning',
            self::Processing => 'secondary',
            self::Cancelled => 'danger',
            self::Updated => 'info',
            self::Complete => 'success',
        };
    }

    public static function toOptions(): array
    {
        return array_map(fn (self $s) => $s->getLabel(), self::cases());
    }
}
