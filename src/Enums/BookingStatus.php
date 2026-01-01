<?php

namespace Adultdate\FilamentBooking\Enums;

use Filament\Support\Colors\Color;
use Filament\Tables\Columns\Enums\BadgeColor;

enum BookingStatus: string
{
    
    case Booked = 'booked';
    case Confirmed = 'confirmed';
    case Processing = 'processing';
    case Cancelled = 'cancelled';
    case Problem = 'problem';
    case Updated = 'updated';
    case Incomplete = 'incomplete';
    case Complete = 'completed';

    public function getLabel(): string
    {
        return match ($this) {

            self::Booked => 'Booked',
            self::Confirmed => 'Confirmed',
            self::Processing => 'Processing',
            self::Cancelled => 'Cancelled',
            self::Problem => 'Problem',
            self::Updated => 'Updated',
            self::Incomplete => 'Incomplete',
            self::Complete => 'Completed',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Booked => 'primary',
            self::Confirmed => 'warning',
            self::Processing => 'secondary',
            self::Cancelled => 'danger',
            self::Problem => 'danger',
            self::Updated => 'info',
            self::Incomplete => 'warning',
            self::Complete => 'success',
        };
    }

    public static function toOptions(): array
    {
        return array_map(fn (self $s) => $s->getLabel(), self::cases());
    }
}
