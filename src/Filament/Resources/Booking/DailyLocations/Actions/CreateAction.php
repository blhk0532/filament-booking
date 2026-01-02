<?php

namespace Adultdate\FilamentBooking\Filament\Resources\Booking\DailyLocations\Actions;

use Filament\Actions\CreateAction as BaseCreateAction;
use Filament\Actions\Action;
use Filament\Schemas\Schema as FilamentSchema;
use Adultdate\FilamentBooking\Filament\Widgets\FullCalendarWidget;
use Adultdate\FilamentBooking\Filament\Resources\Booking\DailyLocations\Widgets\EventCalendar;

class CreateAction extends BaseCreateAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->model(
            fn (EventCalendar $livewire) => $livewire->getModel()
        );

        $this->schema(
            fn (FilamentSchema $schema, CreateAction $action, EventCalendar $livewire) => $livewire->getFormSchemaForModel($schema, $livewire->getModel())
        );

        $this->after(
            fn (EventCalendar $livewire) => $livewire->refreshRecords()
        );

        $this->modalFooterActions(fn (CreateAction $action, EventCalendar $livewire) => [
            // Keep the default form actions (Create, Create & create another, etc.)
            ...$livewire->getCachedFormActions(),

            // Add a "Block Period" button before the cancel button so it is visible
            Action::make('block-period')
                ->label('Block Period')
                ->icon('heroicon-o-ban')
                ->color('danger')
                ->button()
                ->action(fn () => $livewire->dispatch('block-period')),

            // Keep the cancel button
            $action->getModalCancelAction(),
        ]);

        $this->cancelParentActions();
    }
}
