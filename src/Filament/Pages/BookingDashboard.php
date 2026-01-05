<?php

namespace Adultdate\FilamentBooking\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Adultdate\FilamentBooking\Filament\Pages\Dashboard as BaseAppDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Components\Section;
use BackedEnum; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;



class BookingDashboard extends BaseAppDashboard
{

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartPie;

    protected static ?string $navigationLabel = 'Dash';

     protected static ?string $title = '';

     protected static ?string $slug = 'booking-dashboard';

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public static function getNavigationLabel(): string                       
    {
        return '' . Str::ucfirst(Auth::user()->name) ?? 'User';
    }

    public static function getNavigationBadge(): ?string
    {
        //  return now()->format('H:m');
        return 'APP';
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    use HasFiltersForm;

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Select::make('businessCustomersOnly')
                            ->boolean(),
                        DatePicker::make('startDate')
                            ->maxDate(fn (Get $get) => $get('endDate') ?: now()),
                        DatePicker::make('endDate')
                            ->minDate(fn (Get $get) => $get('startDate') ?: now())
                            ->maxDate(now()),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
            ]);
    }

    public function getPermissionCheckClosure(): \Closure
    {
        return fn (string $widgetClass) => true;
    }

}
