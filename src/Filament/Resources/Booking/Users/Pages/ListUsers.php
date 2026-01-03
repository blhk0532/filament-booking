<?php

namespace Adultdate\FilamentBooking\Filament\Resources\Booking\Users\Pages;

use Adultdate\FilamentBooking\Filament\Resources\Booking\Users\UserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected static ?string $title = 'ServiceProvider s &Booking Agents';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            // ServiceProvider s
            'service' => Tab::make('Service Providers')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('role', 'service')),

            // Booking Agents
            'booking' => Tab::make('Booking Agents')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('role', 'booking')),
            // 'all' => Tab::make('All Users'),

        ];
    }
}
