<?php

namespace Adultdate\FilamentBooking\Filament\Resources\Booking\DailyLocations\Actions;

use Filament\Actions\EditAction as BaseEditAction;
use Adultdate\FilamentBooking\Filament\Widgets\FullCalendarWidget;
use Adultdate\FilamentBooking\Filament\Resources\Booking\DailyLocations\Widgets\EventCalendar;

class EditAction extends BaseEditAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->model(
            fn (EventCalendar $livewire) => $livewire->getModel()
        );

        $this->record(
            fn (EventCalendar $livewire) => $livewire->getRecord()
        );

        $this->schema(
            fn (EventCalendar $livewire) => $livewire->getFormSchema()
        );

        $this->after(
            fn (EventCalendar $livewire) => $livewire->refreshRecords()
        );

        $this->cancelParentActions();
    }
}
