<?php

namespace Adultdate\FilamentShop\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ServiceStatus: string implements HasColor, HasIcon, HasLabel
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
            self::Booked => 'info',
            self::Changed => 'warning',
            self::Processing => 'primary',
            self::Cancelled => 'danger',
            self::Updated => 'gray',
            self::Complete => 'success',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Booked => 'heroicon-m-calendar-days',
            self::Changed => 'heroicon-m-arrow-path',
            self::Processing => 'heroicon-m-cog-6-tooth',
            self::Cancelled => 'heroicon-m-x-circle',
            self::Updated => 'heroicon-m-pencil-square',
            self::Complete => 'heroicon-m-check-badge',
        };
    }
}