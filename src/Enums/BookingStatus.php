<?php

namespace Adultdate\FilamentShop\Enums;

use Filament\Support\Colors\Color;
use Filament\Tables\Columns\Enums\BadgeColor;

enum BookingStatus: string
{
    case Booked = 'booked';
    case Changed = 'changed';
    case Processing = 'processing';
    case Cancelled = 'cancelled';
    case Updated = 'updated';
    case Complete = 'complete';

    public function getLabel(): string
    {
        return match ($this) {
            self::Booked => 'Booked',
            self::Changed => 'Changed',
            self::Processing => 'Processing',
            self::Cancelled => 'Cancelled',
            self::Updated => 'Updated',
            self::Complete => 'Complete',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Booked => 'primary',
            self::Changed => 'warning',
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
