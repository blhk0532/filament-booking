<?php

namespace Adultdate\FilamentBooking;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Assets\Theme;
use Filament\Support\Colors;
use Filament\Support\Facades\FilamentAsset;

class FilamentBooking implements Plugin
{
    public function getId(): string
    {
        return 'filament-booking';
    }

    public function register(Panel $panel): void
    {
        FilamentAsset::register([
            Theme::make('filament-booking', __DIR__ . '/../resources/dist/filament-booking.css'),
        ]);

        $panel
            ->font('DM Sans')
            ->primaryColor(\Filament\Support\Colors\Color::Amber)
            ->secondaryColor(\Filament\Support\Colors\Color::Gray)
            ->warningColor(\Filament\Support\Colors\Color::Amber)
            ->dangerColor(\Filament\Support\Colors\Color::Rose)
            ->successColor(\Filament\Support\Colors\Color::Green)
            ->grayColor(\Filament\Support\Colors\Color::Gray)
            ->theme('filament-booking');
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
