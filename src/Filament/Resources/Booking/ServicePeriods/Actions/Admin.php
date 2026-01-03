<?php

namespace Adultdate\FilamentBooking\Filament\Resources\Booking\ServicePeriods\Actions;

use Filament\Actions\CreateAction as BaseCreateAction;
use Filament\Actions\Action;


class AdminAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function adminAction(): Action
    {
        return Action::make('admin')
            ->requiresConfirmation()
            ->action(function (array $arguments) {
                dd('Admin action called', $arguments);
            });
    }
}
