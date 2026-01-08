<?php

namespace Adultdate\FilamentBooking\Filament\Resources\BookingCalendars\Schemas;

use Adultdate\FilamentBooking\Models\User;
use App\UserRole;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BookingCalendarForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Select::make('creator_id')
                    ->relationship('creator', 'name')
                    ->required(),
                Select::make('owner_id')
                    ->relationship('owner', 'name', fn ($query) => $query->where('role', UserRole::SERVICE))
                    ->required(),
                Select::make('access')
                    ->label('Users with Access')
                    ->multiple()
                    ->options(User::all()->pluck('name', 'id'))
                    ->searchable(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
