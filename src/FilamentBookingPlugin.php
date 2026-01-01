<?php

namespace Adultdate\FilamentBooking;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Navigation\NavigationGroup;
use Adultdate\FilamentBooking\Filament\Clusters\Products\ProductsCluster;
use Adultdate\FilamentBooking\Filament\Clusters\Services\ServicesCluster;
use Adultdate\FilamentBooking\Filament\Pages\BookingCalendar;
use Adultdate\FilamentBooking\Filament\Resources\Booking\Customers\CustomerResource;
use Adultdate\FilamentBooking\Filament\Resources\Booking\Orders\OrderResource;
use Adultdate\FilamentBooking\Filament\Widgets\BookingCalendarWidget;
use Adultdate\FilamentBooking\Filament\Widgets\CustomersChart;
use Adultdate\FilamentBooking\Filament\Widgets\LatestOrders;
use Adultdate\FilamentBooking\Filament\Widgets\OrdersChart;
use Adultdate\FilamentBooking\Filament\Widgets\StatsOverviewWidget;
use Adultdate\FilamentBooking\Filament\Resources\Booking\DailyLocations\DailyLocationResource;

class FilamentBookingPlugin implements Plugin
{
    public function getId(): string
    {
        return 'filament-booking';
    }

    public function register(Panel $panel): void
    {
            $panel
            ->discoverClusters(in: app_path('../vendor/adultdate/filament-booking/src/Filament/Clusters'), for: 'Adultdate\\FilamentBooking\\Filament\\Clusters')
            ->databaseNotifications()
            ->pages([
                BookingCalendar::class,
            ])
            ->resources([
                CustomerResource::class,
                OrderResource::class,
                DailyLocationResource::class,
            ])
            ->widgets([
                BookingCalendarWidget::class,
                CustomersChart::class,
                LatestOrders::class,
                OrdersChart::class,
                StatsOverviewWidget::class,
            ]); 
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
