<?php

namespace Adultdate\FilamentBooking\Filament\Resources\Booking\DailyLocations\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Section;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DailyLocationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Daily Location')
                    ->schema([
                        Hidden::make('daily_location_id'),
                        DatePicker::make('date')
                            ->label('Date')
                            ->required()
                            ->native(false),

                        Select::make('service_user_id')
                            ->label('Service User')
                            ->options(User::where('role', 'service')->pluck('name', 'id'))
                            ->searchable()
                            ->required(),

                        TextInput::make('location')
                            ->label('Location')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columns(1),
            ]);
    }
}
