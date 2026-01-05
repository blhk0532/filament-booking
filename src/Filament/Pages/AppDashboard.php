<?php

namespace Adultdate\FilamentBooking\Filament\Pages;

use Adultdate\FilamentBooking\Traits\HasDashArrange;
use BackedEnum;
use Filament\Pages\Dashboard as BaseAdminDashboard;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AppDashboard extends BaseAdminDashboard
{
    use HasDashArrange;

    protected static ?string $slug = 'dashboard';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartPie;

    protected static ?string $navigationLabel = 'Dash';

    protected static ?string $title = '';

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public static function getNavigationLabel(): string
    {
        return ''.Str::ucfirst(Auth::user()->name) ?? 'User';
    }

    public static function getNavigationBadge(): ?string
    {
        //  return now()->format('H:m');
        return 'APP';
    }

    public function getWidgets(): array
    {
        return [
            \Adultdate\FilamentBooking\Filament\Widgets\StatsOverviewWidget::class,
            \Adultdate\FilamentBooking\Filament\Widgets\AccountWidget::class,
            \Adultdate\FilamentBooking\Filament\Widgets\FilamentInfosWidget::class,
        ];
    }

    public function getPermissionCheckClosure(): \Closure
    {
        return fn (string $widgetClass) => true;
    }
}
