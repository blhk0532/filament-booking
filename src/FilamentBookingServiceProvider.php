<?php

namespace Adultdate\FilamentBooking;

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Livewire\Features\SupportTesting\Testable;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Adultdate\FilamentBooking\Commands\FilamentBookingCommand;
use Adultdate\FilamentBooking\Testing\TestsFilamentBooking;
use Filament\Facades\Filament;

class FilamentBookingServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-booking';

    public static string $viewNamespace = 'filament-booking';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name(static::$name)
            ->hasCommands($this->getCommands())
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->startWith(function (InstallCommand $installCommand) {
                        $installCommand->info('Creating storage directories...');
                        $this->createStorageDirectories();
                    })
                    ->endWith(function (InstallCommand $installCommand) {
                        $installCommand->info('Running storage:link command...');
                        $this->runStorageLink();
                        $installCommand->info('Filament Booking installed successfully!');
                    })
                    ->askToStarRepoOnGitHub('adultdate/filament-booking');
            });

        $configFileName = $package->shortName();

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

        if (file_exists($package->basePath('/../database/migrations'))) {
            $package->hasMigrations($this->getMigrations());
        }

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
    }

    public function packageRegistered(): void {}

    public function packageBooted(): void
    {
        // Register observers
        \Adultdate\FilamentBooking\Models\Booking\Booking::observe(\Adultdate\FilamentBooking\Observers\BookingObserver::class);

        // Asset Registration
        FilamentAsset::registerScriptData(
            $this->getScriptData(),
            $this->getAssetPackageName()
        );

        // Icon Registration
        FilamentIcon::register($this->getIcons());

        // Register Media Library Routes
        $this->registerMediaRoutes();

        // Register Livewire component aliases for calendar widgets
        if (class_exists('\Livewire\Livewire')) {
            \Livewire\Livewire::component('adultdate.filament-booking.filament.widgets.full-calendar-widget', \Adultdate\FilamentBooking\Filament\Widgets\FullCalendarWidget::class);
            \Livewire\Livewire::component('adultdate.filament-booking.filament.widgets.booking-calendar-widget', \Adultdate\FilamentBooking\Filament\Widgets\BookingCalendarWidget::class);
            \Livewire\Livewire::component('adultdate.filament-booking.filament.widgets.event-calendar', \Adultdate\FilamentBooking\Filament\Widgets\LocationCalendarWidget::class);
            \Livewire\Livewire::component('adultdate.filament-booking.filament.resources.booking.daily-locations.widgets.event-calendar', \Adultdate\FilamentBooking\Filament\Resources\Booking\DailyLocations\Widgets\EventCalendar::class);
            // Register resource-scoped widget alias so Livewire can resolve
            // widgets referenced by their Filament resource path.
            \Livewire\Livewire::component(
                'adultdate.filament-booking.filament.resources.booking.daily-locations.widgets.location-calendar-widget',
                \Adultdate\FilamentBooking\Filament\Resources\Booking\DailyLocations\Widgets\LocationCalendarWidget::class
            );
            \Livewire\Livewire::component(
                'adultdate.filament-booking.filament.widgets.bokning-calendar',
                \Adultdate\FilamentBooking\Filament\Clusters\Services\Resources\Bookings\Widgets\BokningCalendar::class
            );
            \Livewire\Livewire::component(
                'adultdate.filament-booking.filament.widgets.booking-calendar',
                \Adultdate\FilamentBooking\Filament\Clusters\Services\Resources\Bookings\Widgets\BookingCalendar::class
            );
            \Livewire\Livewire::component('adultdate.filament-booking.filament.widgets.event-calendar', \Adultdate\FilamentBooking\Filament\Widgets\EventCalendar::class);

        }

        FilamentAsset::register(
            assets: [
                AlpineComponent::make(
                    'calendar',
                    __DIR__ . '/../dist/js/calendar.js',
                ),
                AlpineComponent::make(
                    'calendar-context-menu',
                    __DIR__ . '/../dist/js/calendar-context-menu.js',
                ),
                AlpineComponent::make(
                    'calendar-event',
                    __DIR__ . '/../dist/js/calendar-event.js',
                ),
                AlpineComponent::make('filament-booking-alpine', __DIR__ . '/../resources/dist/filament-booking.js'),
                AlpineComponent::make('filament-fullcalendar-alpine', __DIR__ . '/../dist/js/filament-fullcalendar.js'),
                Css::make('calendar-styles', 'https://cdn.jsdelivr.net/npm/@event-calendar/build@4.5.0/dist/event-calendar.min.css'),
                Js::make('calendar-script', 'https://cdn.jsdelivr.net/npm/@event-calendar/build@4.5.0/dist/event-calendar.min.js'),
            ],
            package: 'adultdate/filament-booking'
        );

        // Ensure views are available under the legacy namespace used across the package
        $viewsPath = __DIR__ . '/../resources/views';
        if (is_dir($viewsPath)) {
            $this->loadViewsFrom($viewsPath, 'adultdate/filament-booking');
        }

        // Migration Publishing
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'adultdate/filament-booking-migrations');

        // Testing
        Testable::mixin(new TestsFilamentBooking);

        // Register Filament resources when Filament is available
        if (class_exists(Filament::class)) {
            Filament::serving(function (): void {
                Filament::registerResources([
                    \Adultdate\FilamentBooking\Filament\Resources\BookingCalendars\BookingCalendarResource::class,
                ]);
            });
        }
    }

    protected function getAssetPackageName(): ?string
    {
        return 'adultdate/filament-booking';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {

        $distPath = __DIR__ . '/../../dist/js';

        if (is_dir($distPath)) {
            return [
                // Ensure EventCalendar runtime is loaded first so it exposes a global
                Js::make(
                    'event-calendar-script',
                    'https://cdn.jsdelivr.net/npm/@event-calendar/build@4.5.0/dist/event-calendar.min.js'
                ),
                Css::make(
                    'event-calendar-styles',
                    'https://cdn.jsdelivr.net/npm/@event-calendar/build@4.5.0/dist/event-calendar.min.css'
                ),
                // Calendar Alpine components
                AlpineComponent::make(
                    'calendar',
                    $distPath . '/calendar.js'
                ),
                AlpineComponent::make(
                    'calendar-context-menu',
                    $distPath . '/calendar-context-menu.js'
                ),
                AlpineComponent::make(
                    'calendar-event',
                    $distPath . '/calendar-event.js'
                ),
                AlpineComponent::make(
                    'filament-fullcalendar-alpine',
                    $distPath . '/filament-fullcalendar.js'
                ),
            ];
        }

        return [];

    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [
            FilamentBookingCommand::class,
        ];
    }

    /**
     * @return array<string>
     */
    protected function getIcons(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getRoutes(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getScriptData(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getMigrations(): array
    {
        return [
            'create_booking_addressable_table', 
            'create_booking_addresses_table', 
            'create_booking_clients_table',
            'create_booking_comments_table', 
            'create_booking_exports_table', 
            'create_booking_imports_table', 
            'create_booking_media_table', 
            'create_booking_notifications_table', 
            'create_booking_payments_table', 
            'create_booking_settings_table', 
            'create_booking_brands_table', 
            'create_booking_categories_table', 
            'create_booking_category_product_table', 
            'create_booking_customers_table', 
            'create_booking_order_items_table', 
            'create_booking_orders_table', 
            'create_booking_products_table', 
            'create_booking_tag_tables',
        ];
    }

    protected function createStorageDirectories(): void
    {
        $directories = [
            storage_path('app/product-images'),
            storage_path('app/public'),
            storage_path('app/private'),
        ];

        $filesystem = app(Filesystem::class);

        foreach ($directories as $directory) {
            if (!$filesystem->exists($directory)) {
                $filesystem->makeDirectory($directory, 0755, true);
            }
        }
    }

    protected function runStorageLink(): void
    {
        $exitCode = Artisan::call('storage:link');

        if ($exitCode === 0) {
            // Storage link was successful
            return;
        }

        // If storage link failed, try to create the links manually
        $this->createStorageLinks();
    }

    protected function createStorageLinks(): void
    {
        $filesystem = app(Filesystem::class);
        $publicStorage = public_path('storage');
        $appStorage = storage_path('app/public');

        // Remove existing link if it exists
        if ($filesystem->exists($publicStorage)) {
            $filesystem->delete($publicStorage);
        }

        // Create the storage link
        $filesystem->link($appStorage, $publicStorage);

        // Create product-images link
        $productImagesLink = public_path('storage/product-images');
        $productImagesTarget = storage_path('app/product-images');

        if (!$filesystem->exists($productImagesLink)) {
            $filesystem->link($productImagesTarget, $productImagesLink);
        }
    }

    protected function registerMediaRoutes(): void
    {
        // Register media library routes for serving media files and conversions
        app('router')->get('/storage/product-images/{mediaId}/conversions/{conversionName}', function ($mediaId, $conversionName) {
            $media = \Spatie\MediaLibrary\MediaCollections\Models\Media::findOrFail($mediaId);

            if ($media->hasGeneratedConversion($conversionName)) {
                return response()->file($media->getPath($conversionName), [
                    'Access-Control-Allow-Origin' => '*',
                    'Access-Control-Allow-Methods' => 'GET, POST, OPTIONS',
                    'Access-Control-Allow-Headers' => 'Content-Type, Authorization',
                ]);
            }

            abort(404);
        })->name('media.conversion');

        app('router')->get('/storage/product-images/{mediaId}/{filename}', function ($mediaId, $filename) {
            $media = \Spatie\MediaLibrary\MediaCollections\Models\Media::findOrFail($mediaId);

            return response()->file($media->getPath(), [
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'GET, POST, OPTIONS',
                'Access-Control-Allow-Headers' => 'Content-Type, Authorization',
            ]);
        })->name('media.file');
    }
}
