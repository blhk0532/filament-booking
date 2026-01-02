<?php

namespace Adultdate\FilamentBooking\Filament\Resources\Booking\DailyLocations\Widgets;

use Adultdate\FilamentBooking\Attributes\CalendarEventContent;
use Adultdate\FilamentBooking\Attributes\CalendarSchema;
use Adultdate\FilamentBooking\Concerns\CanRefreshCalendar;
use Adultdate\FilamentBooking\Concerns\HasOptions;
use Adultdate\FilamentBooking\Concerns\HasSchema;
use Adultdate\FilamentBooking\Concerns\InteractsWithCalendar;
use Adultdate\FilamentBooking\Concerns\InteractsWithEventRecord;
use Adultdate\FilamentBooking\Contracts\HasCalendar;
use Adultdate\FilamentBooking\Enums\Priority;
use Adultdate\FilamentBooking\Filament\Widgets\Concerns\CanBeConfigured;
use Adultdate\FilamentBooking\Filament\Widgets\Concerns\InteractsWithEvents;
use Adultdate\FilamentBooking\Filament\Widgets\Concerns\InteractsWithRawJS;
use Adultdate\FilamentBooking\Filament\Widgets\Concerns\InteractsWithRecords;
use Adultdate\FilamentBooking\Filament\Widgets\SimpleCalendarWidget;
use Adultdate\FilamentBooking\Models\BookingMeeting;
use Adultdate\FilamentBooking\Models\BookingSprint;
use Adultdate\FilamentBooking\Models\BookingServicePeriod;
use Adultdate\FilamentBooking\Models\Booking\DailyLocation;
use Adultdate\FilamentBooking\Models\Booking\Booking;
use Adultdate\FilamentBooking\Filament\Actions\CreateAction as FilamentCreateAction;
use Adultdate\FilamentBooking\Models\CalendarSettings;
use Adultdate\FilamentBooking\ValueObjects\DateClickInfo;
use Adultdate\FilamentBooking\ValueObjects\DateSelectInfo;
use Adultdate\FilamentBooking\ValueObjects\EventDropInfo;
use Adultdate\FilamentBooking\ValueObjects\EventResizeInfo;
use Adultdate\FilamentBooking\ValueObjects\FetchInfo;
// use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Adultdate\FilamentBooking\Filament\Resources\Booking\DailyLocations\Actions\CreateAction as DailyLocationCreateAction;
use Adultdate\FilamentBooking\Filament\Resources\Booking\DailyLocations\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Adultdate\FilamentBooking\Enums\BookingStatus;
use Adultdate\FilamentBooking\Models\Booking\BookingLocation;
use Adultdate\FilamentBooking\Models\Booking\BookingSchedule;
use Adultdate\FilamentBooking\Models\Booking\Client;
use Adultdate\FilamentBooking\Models\Booking\Service;
use App\Models\User;
use Carbon\Carbon;


final class EventCalendar extends SimpleCalendarWidget implements HasCalendar
{

   

    use CanBeConfigured, CanRefreshCalendar, HasOptions, HasSchema, InteractsWithCalendar, InteractsWithEventRecord, InteractsWithEvents, InteractsWithRawJS, InteractsWithRecords {
        // Prefer the contract-compatible refreshRecords (chainable) from CanRefreshCalendar
        CanRefreshCalendar::refreshRecords insteadof InteractsWithEvents;

        // Keep the frontend-only refresh available under an alias if needed
        InteractsWithEvents::refreshRecords as refreshRecordsFrontend;

        // Resolve getOptions collision: prefer HasOptions' getOptions which merges config and options
        HasOptions::getOptions insteadof CanBeConfigured;

        InteractsWithEventRecord::getEloquentQuery insteadof InteractsWithRecords;
        InteractsWithEvents::onEventClickLegacy insteadof InteractsWithCalendar;
        InteractsWithEvents::onDateSelectLegacy insteadof InteractsWithCalendar;
        InteractsWithEvents::onEventDropLegacy insteadof InteractsWithCalendar;
        InteractsWithEvents::onEventResizeLegacy insteadof InteractsWithCalendar;
    }

    protected static ?int $sort = 1;
 // public Model|string|null $model = Booking::class;

 //  public function getModel(): string
 //  {
 //      $model = Booking::class;
 //      return $model;
 //  }

    public function schema(Schema $schema): Schema
    {
        return $schema->schema([]);
    }

    public function getOptions(): array
    {
        return $this->getConfig();
    }

    #[CalendarSchema(model: DailyLocation::class)]
    protected function dailyLocationSchema(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('location')->required(),
        ]);
    }

    #[CalendarSchema(model: BookingMeeting::class)]
    protected function bookingMeetingSchema(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('title')->required(),
        ]);
    }

    #[CalendarSchema(model: BookingSprint::class)]
    protected function bookingSprintSchema(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('title')->required(),
        ]);
    }

    #[CalendarSchema(model: BookingServicePeriod::class)]
    protected function bookingServicePeriodSchema(Schema $schema): Schema
    {
        return $schema->schema([
            Select::make('service_user_id')
                ->label('Service User')
                ->relationship('serviceUser', 'name')
                ->required(),
            TextInput::make('service_location')
                ->label('Location')
                ->required(),
            TimePicker::make('start_time')
                ->label('Start Time')
                ->seconds(false)
                ->required(),
            TimePicker::make('end_time')
                ->label('End Time')
                ->seconds(false)
                ->required(),
            TextInput::make('period_type')
                ->label('Period Type')
                ->default('unavailable')
                ->required(),
        ]);
    }

    public function getConfig(): array
    {
        $settings = CalendarSettings::where('user_id', Auth::id())->first();

        $openingStart = $settings?->opening_hour_start?->format('H:i:s') ?? '09:00:00';
        $openingEnd = $settings?->opening_hour_end?->format('H:i:s') ?? '17:00:00';

        $config = [
            'view' => 'dayGridMonth',
            'headerToolbar' => [
                'start' => 'prev,next today',
                'center' => 'title',
                'end' => 'dayGridMonth,timeGridWeek,timeGridDay',
            ],
            'nowIndicator' => true,
            'views' => [
                'timeGridDay' => [
                    'slotMinTime' => $openingStart,
                    'slotMaxTime' => '24:00:00',
                    'slotHeight' => 60,
                ],
                'timeGridWeek' => [
                    'slotMinTime' => $openingStart,
                    'slotMaxTime' => '24:00:00',
                    'slotHeight' => 60,
                ],
                'timeGridMonth' => [
                    'slotMinTime' => $openingStart,
                    'slotMaxTime' => '24:00:00',
                    'slotHeight' => 60,
                ],
            ],
        ];

        return $config;
    }

    protected function getEvents(FetchInfo $info): Collection|array|Builder
    {
        $start = $info->start->toMutable()->startOfDay();
        $end = $info->end->toMutable()->endOfDay();

        \Illuminate\Support\Facades\Log::info('getEvents called', ['start' => $start, 'end' => $end]);

        $dailyLocations = DailyLocation::query()    
            ->whereBetween('date', [$start, $end])
            ->with(['serviceUser'])
            ->get();

        $meetings = BookingMeeting::query()
            ->withCount('users')
            ->whereDate('ends_at', '>=', $start)
            ->whereDate('starts_at', '<=', $end)
            ->get();

        $sprints = BookingSprint::query()
            ->whereDate('ends_at', '>=', $start)
            ->whereDate('starts_at', '<=', $end)
            ->get();

        $events = collect()
            ->push(...$dailyLocations)
            ->push(...$meetings)
            ->push(...$sprints);

        \Illuminate\Support\Facades\Log::info('Events returned', ['count' => $events->count()]);

        return $events;

    }

    protected function getEventClickContextMenuActions(): array
    {
        return [
            $this->viewAction()->label('View'),
            $this->editAction()->label('Edit'),
            $this->deleteAction()->label('Delete'),
        ];
    }


      public function getFormSchema(): array
    {
        return [
            Select::make('booking_client_id')
                ->label('Client')
                ->options(Client::pluck('name', 'id'))
                ->searchable()
                ->preload()
                ->createOptionForm([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('email')
                        ->email()
                        ->maxLength(255),
                    TextInput::make('phone')
                        ->tel()
                        ->maxLength(255),
                    TextInput::make('address')
                        ->maxLength(255),
                    TextInput::make('city')
                        ->maxLength(255),
                    TextInput::make('postal_code')
                        ->maxLength(20),
                    TextInput::make('country')
                        ->default('Sweden')
                        ->dehydrated(false)
                        ->hidden(),
                ])
                ->createOptionUsing(function (array $data) {
                    $data['country'] = 'Sweden';
                    $client = Client::create($data);
                    return $client->id;
                })
                ->required(),

            Select::make('service_id')
                ->label('Service')
                ->options(Service::pluck('name', 'id'))
                ->searchable()
                ->preload()
                ->required(),

            Select::make('booking_location_id')
                ->label('Location')
                ->options(BookingLocation::where('is_active', true)->pluck('name', 'id'))
                ->searchable()
                ->preload()
                ->required(),

            Select::make('service_user_id')
                ->label('Service Technician')
                ->options(User::pluck('name', 'id'))
                ->searchable()
                ->preload(),

            DatePicker::make('service_date')
                ->label('Service Date')
                ->required()
                ->native(false),

            TimePicker::make('start_time')
                ->label('Start Time')
                ->required()
                ->seconds(false)
                ->native(false),

            TimePicker::make('end_time')
                ->label('End Time')
                ->required()
                ->seconds(false)
                ->native(false),

            Select::make('status')
                ->label('Status')
                ->options(BookingStatus::class)
                ->default(BookingStatus::Booked->value)
                ->required(),

            TextInput::make('total_price')
                ->label('Total Price')
                ->numeric()
                ->prefix('SEK'),

            Textarea::make('notes')
                ->label('Notes')
                ->rows(3),

            Textarea::make('service_note')
                ->label('Service Note')
                ->rows(3),

            Repeater::make('items')
                ->label('Booking Items')
                ->schema([
                    Select::make('booking_service_id')
                        ->label('Service')
                        ->options(Service::pluck('name', 'id'))
                        ->searchable()
                        ->required(),

                    TextInput::make('qty')
                        ->label('Quantity')
                        ->numeric()
                        ->default(1)
                        ->minValue(1)
                        ->required(),

                    TextInput::make('unit_price')
                        ->label('Unit Price')
                        ->numeric()
                        ->prefix('SEK')
                        ->default(0)
                        ->required(),
                ])
                ->columns(3)
                ->defaultItems(0)
                ->collapsible(),
        ];
    }

    protected function getDateClickContextMenuActions(): array
    {
        return [
            CreateAction::make()
                ->label('New Booking')
                ->schema($this->getFormSchema())
                ->mountUsing(function ($formOrSchema, array $arguments) {
                    if ($formOrSchema instanceof Schema) {
                        $formOrSchema->fill([]);
                    } elseif (is_object($formOrSchema) && method_exists($formOrSchema, 'fill')) {
                        $formOrSchema->fill([]);
                    }

                    if (!isset($arguments['start']) && !isset($arguments['service_date'])) {
                        return;
                    }

                    $timezone = config('app.timezone');
                    
                    if (isset($arguments['service_date']) || isset($arguments['start_time'])) {
                        $values = [
                            'service_date' => $arguments['service_date'] ?? null,
                            'start_time' => $arguments['start_time'] ?? null,
                            'end_time' => $arguments['end_time'] ?? null,
                        ];
                    } else {
                        $start = Carbon::parse($arguments['start'], $timezone);
                        $values = ['service_date' => $start->format('Y-m-d')];

                        if ($start->format('H:i:s') !== '00:00:00') {
                            $values['start_time'] = $start->format('H:i');
                        }

                        if (isset($arguments['end'])) {
                            $end = Carbon::parse($arguments['end'], $timezone);
                            if ($end->format('H:i:s') !== '00:00:00') {
                                $values['end_time'] = $end->format('H:i');
                            }
                        }
                    }

                    if ($formOrSchema instanceof Schema) {
                        $formOrSchema->fillPartially($values, array_keys($values));
                        return;
                    }

                    if (is_object($formOrSchema) && method_exists($formOrSchema, 'fill')) {
                        $formOrSchema->fill($values);
                        return;
                    }
                })
                ->mutateDataUsing(function (array $data): array {
                    $data['number'] = 'BK-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
                    $data['booking_user_id'] = Auth::id();
                    $data['currency'] = 'SEK';
                    $data['is_active'] = true;

                    if (isset($data['service_date']) && isset($data['start_time'])) {
                        $data['starts_at'] = $data['service_date'] . ' ' . $data['start_time'];
                    }
                    if (isset($data['service_date']) && isset($data['end_time'])) {
                        $data['ends_at'] = $data['service_date'] . ' ' . $data['end_time'];
                    }

                    return $data;
                })
                ->after(function (Booking $record, array $data) {
                    if (isset($data['items']) && is_array($data['items'])) {
                        foreach ($data['items'] as $item) {
                            $record->items()->create([
                                'booking_service_id' => $item['booking_service_id'],
                                'qty' => $item['qty'] ?? 1,
                                'unit_price' => $item['unit_price'] ?? 0,
                            ]);
                        }
                    }
                }),
            $this->createAction(DailyLocation::class, 'ctxCreateDailyLocation')
                ->label('Service Location')
                ->icon('heroicon-o-map-pin')
                ->modalHeading('Service Location')
                ->modalWidth('2xl')
                ->mountUsing(function (CreateAction $action, ?Schema $schema, ?DateClickInfo $info): void {
                    if (! $schema || ! $info) {
                        return;
                    }

                    $schema->fill([
                        'date' => $info->date->toDateString(),
                        'service_user_id' => \Illuminate\Support\Facades\Auth::id(),
                        'created_by' => \Illuminate\Support\Facades\Auth::id(),
                    ]);
                }),
            $this->createAction(BookingServicePeriod::class, 'ctxCreateServicePeriod')
                ->label('Add Block Period')
                ->icon('heroicon-o-clock')
                ->modalHeading('Add Block Period')
                ->modalWidth('sm')
                ->mountUsing(function (CreateAction $action, ?Schema $schema, ?DateClickInfo $info): void {
                    if (! $schema || ! $info) {
                        return;
                    }

                    $schema->fill([
                        'service_date' => $info->date->toDateString(),
                        'created_by' => \Illuminate\Support\Facades\Auth::id(),
                    ]);
                }),

        ];
    }

    protected function getDateSelectContextMenuActions(): array
    {
        return [
            CreateAction::make()
                ->label('Create Bookings')
                ->icon('heroicon-o-calendar-days')
                ->modalHeading('Create Bookings')
                ->modalWidth('4xl')
                ->mountUsing(function (CreateAction $action, ?Schema $schema, ?DateSelectInfo $info): void {
                    if (! $schema || ! $info) {
                        return;
                    }

                    $start = $info->start->toMutable();
                    $end = $info->end->toMutable();

                    if ($info->allDay) {
                        $start->startOfDay();
                        $end->subDay()->endOfDay();
                    }

                    $schema->fill([
                        'priority' => Priority::Medium->value,
                        'starts_at' => $start->format('Y-m-d'),
                        'ends_at' => ($end->greaterThan($start) ? $end : $start->copy()->addDay())->format('Y-m-d'),
                    ]);
                }),
             DailyLocationCreateAction::make()
                ->label('Service Location')
                ->icon('heroicon-o-map-pin')
                ->modalHeading('Service Location')
                ->modalWidth('2xl')
                ->mountUsing(function (CreateAction $action, ?Schema $schema, ?DateClickInfo $info): void {
                    if (! $schema || ! $info) {
                        return;
                    }

                    $schema->fill([
                        'date' => $info->date->toDateString(),
                        'created_by' => \Illuminate\Support\Facades\Auth::id(),
                    ]);
                }),
            $this->createAction(BookingServicePeriod::class, 'ctxCreateServicePeriod')
                ->label('Add Block Period')
                ->icon('heroicon-o-clock')
                ->modalHeading('Add Block Period')
                ->modalWidth('sm')
                ->mountUsing(function (CreateAction $action, ?Schema $schema, ?DateClickInfo $info): void {
                    if (! $schema || ! $info) {
                        return;
                    }

                    $schema->fill([
                        'service_date' => $info->date->toDateString(),
                        'created_by' => \Illuminate\Support\Facades\Auth::id(),
                    ]);
                }),
        ];
    }

    protected function onEventDrop(EventDropInfo $info, Model $event): bool
    {
        if (! $event instanceof BookingMeeting && ! $event instanceof BookingSprint && ! $event instanceof DailyLocation &&  ! $event instanceof BookingServicePeriod) {
            return false;
        }

        $event->forceFill([
            'starts_at' => $info->event->getStart(),
            'ends_at' => $info->event->getEnd(),
        ])->save();

        Notification::make()
            ->title('Event rescheduled')
            ->success()
            ->send();

        $this->refreshRecords();

        return true;
    }

    public function onEventResize(EventResizeInfo $info, Model $event): bool
    {
        if (! $event instanceof Booking) {
            Notification::make()
                ->title('Only periods can be resized')
                ->warning()
                ->send();

            return false;
        }

        $event->forceFill([
            'starts_at' => $info->event->getStart(),
            'ends_at' => $info->event->getEnd(),
        ])->save();

        Notification::make()
            ->title('Sprint duration updated')
            ->success()
            ->send();

        $this->refreshRecords();

        return true;
    }

    #[CalendarEventContent(model: BookingMeeting::class)]
    protected function meetingEventContent(): string
    {
        return view('adultdate/filament-booking::components.calendar.events.meeting')->render();
    }

    #[CalendarEventContent(model: BookingSprint::class)]
    protected function sprintEventContent(): string
    {
        return view('adultdate/filament-booking::components.calendar.events.sprint')->render();
    }

    #[CalendarEventContent(model: DailyLocation::class)]
    protected function locationEventContent(): string
    {
        return view('adultdate/filament-booking::components.calendar.events.location')->render();
    }

    public function mount(): void
    {
        $this->eventClickEnabled = true;
        $this->dateClickEnabled = true;
        $this->eventDragEnabled = true;
        $this->eventResizeEnabled = true;
        $this->dateSelectEnabled = true;
    }

    /**
     * Override the widget heading to hide the default header.
     */
    public function getHeading(): ?string
    {
        return null;
    }

  
}
