<?php

namespace Adultdate\FilamentShop\Filament\Clusters\Services\Resources\Bookings\Pages;

use Adultdate\FilamentShop\Filament\Clusters\Services\Resources\Bookings\BookingResource;

use Adultdate\FilamentShop\Filament\Clusters\Services\Resources\Bookings\Schemas\BookingForm;
use Adultdate\FilamentShop\Models\Booking\Booking;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard\Step;
use Illuminate\Support\Facades\Auth;

class CreateBooking extends CreateRecord
{
    use HasWizard;

    protected static string $resource = BookingResource::class;

    /**
     * @return array<Step>
     */
    protected function getSteps(): array
    {
        return [
            Step::make('Booking Details')
                ->schema([
                    Section::make()
                        ->schema(BookingForm::getDetailsComponents())
                        ->columns(),
                ]),

            Step::make('Booking Items')
                ->schema([
                    Section::make()
                        ->schema([BookingForm::getItemsRepeater()]),
                ]),
        ];
    }

    protected function afterCreate(): void
    {
        /** @var Booking $order */
        $order = $this->record;

        $order->updateTotalPrice();

        /** @var User $user */
        $user = Auth::user();

        Notification::make()
            ->title('New booking')
            ->icon('heroicon-o-calendar-days')
            ->body("**{$order->client?->name} booked {$order->items->count()} services.**")
            ->actions([
                Action::make('View')
                    ->url(BookingResource::getUrl('edit', ['record' => $order])),
            ])
            ->sendToDatabase([$user]);
    }
}

 