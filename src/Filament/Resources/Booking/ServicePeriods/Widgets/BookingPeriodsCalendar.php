<?php

namespace Adultdate\FilamentBooking\Filament\Resources\Booking\ServicePeriods\Widgets;

use Adultdate\FilamentBooking\Models\BookingServicePeriod;
use App\Models\User;
use Filament\Widgets\Widget;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Adultdate\FilamentBooking\Concerns\HasEvents;
use Adultdate\FilamentBooking\Filament\Widgets\FullCalendarWidget;
use Adultdate\FilamentBooking\Models\Booking\DailyLocation;
use Adultdate\FilamentBooking\ValueObjects\FetchInfo;
use Illuminate\Database\Eloquent\Builder;

class BookingPeriodsCalendar extends FullCalendarWidget
{
    use HasEvents;

    protected string $view = 'adultdate/filament-booking::service-periods-fullcalendar';

    protected function getHeading(): ?string
    {
        return 'Calendar';
    }

    protected int|string|array $columnSpan = 'full';

    public function config(): array
    {
        return [
            'initialView' => 'dayGridMonth',
            'headerToolbar' => [
                'start' => 'title',
                'center' => '',
                'end' => 'dayGridMonth,timeGridWeek today prev,next',
            ],
            'nowIndicator' => true,
            'dateClick' => true,
            'eventClick' => true,
        ];
    }

    protected function getEvents(FetchInfo $info): Builder
    {
        $start = $info->start->toMutable()->startOfDay();
        $end = $info->end->toMutable()->endOfDay();

        return DailyLocation::query()
            ->whereBetween('date', [$start, $end])
            ->with(['serviceUser']);
    }

    public function onDateClick(string $date, bool $allDay, ?array $view, ?array $resource): void
    {
        $startDate = \Carbon\Carbon::parse($date);

        $this->mountAction('create', [
            'service_date' => $startDate->format('Y-m-d'),
        ]);
    }
}
