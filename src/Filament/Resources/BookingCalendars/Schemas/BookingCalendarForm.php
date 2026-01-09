<?php

namespace Adultdate\FilamentBooking\Filament\Resources\BookingCalendars\Schemas;

use Adultdate\FilamentBooking\Models\User;
use App\Models\Admin;
use App\UserRole;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use WallaceMartinss\FilamentEvolution\Models\WhatsappInstance;

class BookingCalendarForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('google_calendar_id')
                    ->label('Google Calendar ID')
                    ->helperText('The ID of the Google Calendar to sync with (e.g., your-calendar-id@group.calendar.google.com)')
                    ->placeholder('your-calendar-id@group.calendar.google.com'),
                Select::make('whatsapp_id')
                    ->label('WhatsApp Instance')
                    ->helperText('Connect or refresh instances at /admin/whatsapp-instances?connectInstanceId=')
                    ->options(fn () => WhatsappInstance::query()
                        ->orderBy('name')
                        ->get()
                        ->mapWithKeys(fn (WhatsappInstance $instance) => [
                            $instance->id => sprintf('%s (%s)', $instance->name, $instance->number),
                        ]))
                    ->searchable()
                    ->placeholder('Select WhatsApp instance'),
                Select::make('creator_id')
                    ->relationship('creator', 'name')
                    ->default(Auth::id())
                    ->hidden(),
                Select::make('owner_id')
                    ->relationship('owner', 'name', fn ($query) => $query->where('role', UserRole::SERVICE))
                    ->required(),
                Select::make('notification_user_ids')
                    ->label('Notification Recipients')
                    ->helperText('Users and admins to receive database notifications for new bookings')
                    ->multiple()
                    ->options(collect([
                        ...User::all()->mapWithKeys(fn ($user) => ["user-{$user->id}" => "User: {$user->name}"]),
                        ...Admin::all()->mapWithKeys(fn ($admin) => ["admin-{$admin->id}" => "Admin: {$admin->name}"]),
                    ]))
                    ->searchable()
                    ->placeholder('Select users and admins for notifications'),
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
