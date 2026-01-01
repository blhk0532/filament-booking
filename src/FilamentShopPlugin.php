<?php

namespace Adultdate\FilamentShop;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Navigation\NavigationGroup;
use Adultdate\FilamentShop\Filament\Clusters\Products\ProductsCluster;
use Adultdate\FilamentShop\Filament\Clusters\Services\ServicesCluster;
use Adultdate\FilamentShop\Filament\Resources\Shop\Customers\CustomerResource;
use Adultdate\FilamentShop\Filament\Resources\Shop\Orders\OrderResource;
use Adultdate\FilamentShop\Filament\Widgets\CustomersChart;
use Adultdate\FilamentShop\Filament\Widgets\LatestOrders;
use Adultdate\FilamentShop\Filament\Widgets\OrdersChart;
use Adultdate\FilamentShop\Filament\Widgets\StatsOverviewWidget;

class FilamentShopPlugin implements Plugin
{
    public function getId(): string
    {
        return 'filament-shop';
    }

    public function register(Panel $panel): void
    {
            $panel
            ->discoverClusters(in: app_path('../vendor/adultdate/filament-shop/src/Filament/Clusters'), for: 'Adultdate\\FilamentShop\\Filament\\Clusters')
            ->databaseNotifications()
            ->pages([

            ])
            ->resources([
                CustomerResource::class,
                OrderResource::class,
            ])
            ->widgets([
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
