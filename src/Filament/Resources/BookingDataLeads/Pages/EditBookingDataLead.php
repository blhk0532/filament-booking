<?php

namespace App\Filament\Resources\BookingDataLeads\Pages;

use App\Filament\Resources\BookingDataLeads\BookingDataLeadResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBookingDataLead extends EditRecord
{
    protected static string $resource = BookingDataLeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
