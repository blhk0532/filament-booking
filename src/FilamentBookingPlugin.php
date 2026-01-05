<?php

namespace Adultdate\FilamentBooking;

use Closure;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Navigation\NavigationGroup;
use Filament\Support\Concerns\EvaluatesClosures;
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
use Adultdate\FilamentBooking\Filament\Resources\Booking\ServicePeriods\BookingServicePeriodResource;
use Adultdate\FilamentBooking\Filament\Resources\Booking\DailyLocations\Widgets\EventCalendar;
use Adultdate\FilamentBooking\Filament\Resources\Booking\BookingOutcallQueues\BookingOutcallQueueResource; 
use Adultdate\FilamentBooking\Filament\Resources\Booking\Users\UserResource;
use Adultdate\FilamentBooking\Filament\Pages\Dashboard; 



class FilamentBookingPlugin implements Plugin
{
    use EvaluatesClosures;

    protected array $plugins = ['dayGrid', 'timeGrid', 'interaction', 'list'];

    protected ?string $schedulerLicenseKey = null;

    protected array $config = [];

    protected string | Closure | null $timezone = "Europe/Stockholm";

    protected string | Closure | null $locale = null;

    protected ?bool $editable = null;

    protected ?bool $selectable = null;

    public function getId(): string
    {
        return 'adultdate-booking';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->assets([
                Js::make('event-calendar', 'https://cdn.jsdelivr.net/npm/@event-calendar/build@4.5.0/dist/event-calendar.min.js'),
                Css::make('event-calendar', 'https://cdn.jsdelivr.net/npm/@event-calendar/build@4.5.0/dist/event-calendar.min.css'),
            ])
            ->discoverClusters(in: app_path('../plugins/adultdate/filament-booking/src/Filament/Clusters'), for: 'Adultdate\\FilamentBooking\\Filament\\Clusters')

            ->databaseNotifications()
            ->pages([
                BookingCalendar::class,
            ])
            ->resources([
                CustomerResource::class,
                OrderResource::class,
                DailyLocationResource::class,
                BookingServicePeriodResource::class,
                BookingOutcallQueueResource::class,
                UserResource::class,
            ])
            ->widgets([
                BookingCalendarWidget::class,
                CustomersChart::class,
                LatestOrders::class,
                OrdersChart::class,
                StatsOverviewWidget::class,
                EventCalendar::class,
            ]); 

        FilamentAsset::register([
            AlpineComponent::make('calendar', __DIR__ . '/../dist/js/calendar.js'),
            AlpineComponent::make('calendar-context-menu', __DIR__ . '/../dist/js/calendar-context-menu.js'),
            AlpineComponent::make('calendar-event', __DIR__ . '/../dist/js/calendar-event.js'),
            AlpineComponent::make('filament-fullcalendar-alpine', __DIR__ . '/../dist/js/filament-fullcalendar.js'),
        ], 'adultdate/filament-booking');
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

    public function plugins(array $plugins, bool $merge = true): static
    {
        $this->plugins = $merge ? array_merge($this->plugins, $plugins) : $plugins;

        return $this;
    }

    public function getPlugins(): array
    {
        return $this->plugins;
    }

    public function schedulerLicenseKey(string $schedulerLicenseKey): static
    {
        $this->schedulerLicenseKey = $schedulerLicenseKey;

        return $this;
    }

    public function getSchedulerLicenseKey(): ?string
    {
        return $this->schedulerLicenseKey;
    }

    public function config(array $config): static
    {
        $this->config = $config;

        return $this;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function timezone(string | Closure $timezone): static
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function getTimezone(): string
    {
        return $this->evaluate($this->timezone) ?? config('app.timezone');
    }

    public function locale(string | Closure $locale): static
    {
        $this->locale = $locale;

        return $this;
    }

    public function getLocale(): string
    {
        return $this->evaluate($this->locale) ?? strtolower(str_replace('_', '-', app()->getLocale()));
    }

    public function editable(bool $editable = true): static
    {
        $this->editable = $editable;

        return $this;
    }

    public function isEditable(): bool
    {
        return $this->editable ?? data_get($this->config, 'editable', false);
    }

    public function selectable(bool $selectable = true): static
    {
        $this->selectable = $selectable;

        return $this;
    }

    public function isSelectable(): bool
    {
        return $this->selectable ?? data_get($this->config, 'selectable', true);
    }
}
