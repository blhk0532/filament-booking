<?php

namespace App\Filament\Resources\BookingDataLeads;

use App\Filament\Resources\BookingDataLeads\Pages\CreateBookingDataLead;
use App\Filament\Resources\BookingDataLeads\Pages\EditBookingDataLead;
use App\Filament\Resources\BookingDataLeads\Pages\ListBookingDataLeads;
use App\Filament\Resources\BookingDataLeads\Pages\ViewBookingDataLead;
use App\Filament\Resources\BookingDataLeads\Schemas\BookingDataLeadForm;
use App\Filament\Resources\BookingDataLeads\Schemas\BookingDataLeadInfolist;
use App\Filament\Resources\BookingDataLeads\Tables\BookingDataLeadsTable;
use App\Models\BookingDataLead;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BookingDataLeadResource extends Resource
{
    protected static ?string $model = BookingDataLead::class;

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $navigationLabel = 'Leads';

    protected static \UnitEnum | string | null $navigationGroup = 'Bookings';

    protected static ?int $navigationSort = 11;

    public static function form(Schema $schema): Schema
    {
        return BookingDataLeadForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BookingDataLeadInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BookingDataLeadsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBookingDataLeads::route('/'),
            'create' => CreateBookingDataLead::route('/create'),
            'view' => ViewBookingDataLead::route('/{record}'),
            'edit' => EditBookingDataLead::route('/{record}/edit'),
        ];
    }
}
