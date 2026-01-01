<?php

namespace Adultdate\FilamentBooking\Filament\Clusters\Services\Resources\Bookings\Schemas;

use Adultdate\FilamentBooking\Enums\BookingStatus;
use Adultdate\FilamentBooking\Forms\Components\AddressForm;
use Adultdate\FilamentBooking\Models\Booking\Booking;
use Adultdate\FilamentBooking\Models\Booking\Service;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
class BookingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make()
                            ->schema(static::getDetailsComponents())
                            ->columns(2),

                        Section::make('Booking items')
                            ->afterHeader([
                                Action::make('reset')
                                    ->modalHeading('Are you sure?')
                                    ->modalDescription('All existing items will be removed from the booking.')
                                    ->requiresConfirmation()
                                    ->color('danger')
                                    ->action(fn (Set $set) => $set('items', [])),
                            ])
                            ->schema([
                                static::getItemsRepeater(),
                            ]),
                    ])
                    ->columnSpan(['lg' => fn (?Booking $record) => $record === null ? 3 : 2]),

                Section::make()
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Booking date')
                            ->state(fn (Booking $record): ?string => $record->created_at?->diffForHumans()),

                        TextEntry::make('updated_at')
                            ->label('Last modified at')
                            ->state(fn (Booking $record): ?string => $record->updated_at?->diffForHumans()),
                    ])
                    ->columnSpan(['lg' => 1])
                    ->hidden(fn (?Booking $record) => $record === null),
            ])
            ->columns(3);
    }

    /** @return array<Component> */
    public static function getDetailsComponents(): array
    {
        return [            
            TextInput::make('number')
                ->default('OR-' . random_int(100000, 999999))
                ->disabled()
                ->dehydrated()
                ->required()
                ->maxLength(32)
                ->unique(Booking::class, 'number', ignoreRecord: true),
 
            Select::make('booking_client_id')
                ->relationship('client', 'name')
                ->searchable()
                ->required()
                ->createOptionForm([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('email')
                        ->label('Email address')
                        ->required()
                        ->email()
                        ->maxLength(255)
                        ->unique(),

                    TextInput::make('phone')
                        ->maxLength(255),
                ])
                ->createOptionAction(function (Action $action) {
                    return $action
                        ->modalHeading('Create client')
                        ->modalSubmitActionLabel('Create client')
                        ->modalWidth('lg');
                }),

            Select::make('service_id')
                ->relationship('service', 'name')
                ->searchable()
                ->hidden(),

            Select::make('service_user_id')
                ->label('Service User')
                ->options(User::where('role', 'service')->pluck('name', 'id'))
                ->searchable()
                ->required(),

            TextInput::make('booking_user_id')
                ->hidden()
                ->default(Auth::id())
                ->dehydrated(),

            TextInput::make('service_date')
                ->default(Auth::id())
                ->dehydrated(),

            TextInput::make('start_time')
                ->default(Auth::id())
                ->dehydrated(),

            TextInput::make('end_time')
              
                ->default(Auth::id())
                ->dehydrated(),
            ToggleButtons::make('status')
                ->inline()
                ->options(BookingStatus::class)
                ->required(),

            AddressForm::make('address')
                ->columnSpan('full'),

            RichEditor::make('notes')
                ->columnSpan('full'),
        ];
    }

    public static function getItemsRepeater(): Repeater
    {
        return Repeater::make('items')
            ->relationship()
            ->table([
                TableColumn::make('Service'),
                TableColumn::make('Quantity')
                    ->width(100),
                TableColumn::make('Unit Price')
                    ->width(110),
            ])
            ->schema([
                Select::make('booking_service_id')
                    ->label('Service')
                    ->options(Service::query()->pluck('name', 'id'))
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn ($state, Set $set) => $set('unit_price', Service::find($state)->price ?? 0))
                    ->distinct()
                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                    ->searchable(),

                TextInput::make('qty')
                    ->label('Quantity')
                    ->numeric()
                    ->default(1)
                    ->required(),

                TextInput::make('unit_price')
                    ->disabled()
                    ->dehydrated()
                    ->numeric()
                    ->required(),
            ])
            ->orderColumn('sort')
            ->defaultItems(1)
            ->hiddenLabel();
    }
}
