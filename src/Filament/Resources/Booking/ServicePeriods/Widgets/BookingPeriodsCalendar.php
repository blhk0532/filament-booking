<?php

namespace Adultdate\FilamentBooking\Filament\Resources\Booking\ServicePeriods\Widgets;
use Adultdate\FilamentBooking\Concerns\InteractsWithEventRecord;
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
use Carbon\Carbon;
use Adultdate\FilamentBooking\Enums\BookingStatus;
use Illuminate\Support\Str;
use Adultdate\FilamentBooking\Models\Booking\Booking;
use Adultdate\FilamentBooking\Models\Booking\BookingLocation;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Adultdate\FilamentBooking\Filament\Clusters\Services\Resources\Bookings\Schemas\BookingForm;
use Adultdate\FilamentBooking\Filament\Widgets\Concerns\CanBeConfigured;
use Adultdate\FilamentBooking\Filament\Widgets\Concerns\InteractsWithEvents;
use Adultdate\FilamentBooking\Filament\Widgets\Concerns\InteractsWithRawJS;
use Adultdate\FilamentBooking\Filament\Widgets\Concerns\InteractsWithRecords;
use Adultdate\FilamentBooking\Models\Booking\Client;
use Adultdate\FilamentBooking\Actions as BookingActions;
use Adultdate\FilamentBooking\Concerns\CanRefreshCalendar;
use Adultdate\FilamentBooking\Concerns\HasOptions;
use Adultdate\FilamentBooking\Concerns\HasSchema;
use Adultdate\FilamentBooking\Concerns\InteractsWithCalendar;
use Illuminate\Support\Collection;
use Adultdate\FilamentBooking\Contracts\HasCalendar;
use Illuminate\Support\Facades\Schema;
use Adultdate\FilamentBooking\Models\Booking\Service;
use Adultdate\FilamentBooking\Filament\Resources\Booking\ServicePeriods\Actions\AdminAction;
use Adultdate\FilamentBooking\ValueObjects\DateClickInfo;
use Filament\Actions\Action;


class BookingPeriodsCalendar extends FullCalendarWidget implements HasCalendar
{
    public ?int $recordId = null;

    use CanBeConfigured, CanRefreshCalendar, HasOptions, HasSchema, InteractsWithCalendar, InteractsWithEventRecord, InteractsWithEvents, InteractsWithRawJS, InteractsWithRecords {
        // Prefer the contract-compatible refreshRecords (chainable) from CanRefreshCalendar
        CanRefreshCalendar::refreshRecords insteadof InteractsWithEvents;

        // Keep the frontend-only refresh available under an alias if needed
        InteractsWithEvents::refreshRecords as refreshRecordsFrontend;

        // Resolve getOptions collision: prefer HasOptions' getOptions which merges config and options
        HasOptions::getOptions insteadof CanBeConfigured;

        InteractsWithEventRecord::getEloquentQuery insteadof InteractsWithRecords;
    }
    use InteractsWithEvents {
        InteractsWithEvents::onEventClickLegacy insteadof InteractsWithCalendar;
        InteractsWithEvents::onDateSelectLegacy insteadof InteractsWithCalendar;
        InteractsWithEvents::onEventDropLegacy insteadof InteractsWithCalendar;
        InteractsWithEvents::onEventResizeLegacy insteadof InteractsWithCalendar;
        InteractsWithEvents::refreshRecords insteadof InteractsWithCalendar;
    }

    protected string $view = 'adultdate/filament-booking::service-periods-fullcalendar';

    protected function getHeading(): ?string
    {
        return 'Calendar';
    }

        public function getModel(): string
    {
        return Booking::class;
    }

    public function getModelAlt(): string
    {
        return Booking::class;
    }

    public function getEventModel(): string
    {
        return Booking::class;
    }

    public function getEventRecord(): ?Booking
    {
        return $this->record;
    }

    protected function getEloquentQuery(): Builder
    {
        return app($this->getModel())::query();
    }

    protected int|string|array $columnSpan = 'full';

    public function config(): array
    {
        return [
            'initialView' => 'timeGridWeek',
            'headerToolbar' => [
                'start' => 'prev,next today',
                'center' => 'title',
                'end' => 'dayGridMonth,timeGridWeek,timeGridDay',
            ],
            'nowIndicator' => true,
            'selectable' => true,
            'dateClick' => true,
            'eventClick' => true,
        ];
    }

    public function onDateClick(string $date, bool $allDay, ?array $view, ?array $resource): void
    {
        $startDate = \Carbon\Carbon::parse($date);

        $this->mountAction('create', [
            'service_date' => $startDate->format('Y-m-d'),
        ]);
    }

    public function onDateSelect(string $start, ?string $end, bool $allDay, ?array $view, ?array $resource): void
    {
        $allDay = (bool) $allDay;

        logger()->info('BookingCalendarWidget CALENDAR WAS CLICKED', [
            'start' => $start,
            'end' => $end,
            'allDay' => $allDay,
            'view' => $view,
            'resource' => $resource,
        ]);

        $timezone = config('app.timezone');
        $startDate = Carbon::parse($start, $timezone);

        $startVal = $start;
        $endVal = $end;
        $dateVal = $startDate;

        $startTime = $startVal;
        $endTime = $endVal;

        if ($allDay) {
            logger()->info('BookingCalendarWidget: ALL-DAY CLICK DETECTED!');

            $this->mountAction('createDailyLocation', [
                'date' => $startDate->format('Y-m-d'),
            ]);

            return;
        }

        $data = $this->getDefaultFormData([
            'service_date' => $startDate->format('Y-m-d'),
        ]);

        if (! $allDay && $startDate->format('H:i:s') !== '00:00:00') {
            $data['start_time'] = $startDate->format('H:i');

            if ($end) {
                $endDate = Carbon::parse($end, $timezone);
                if ($endDate->format('H:i:s') !== '00:00:00') {
                    $data['end_time'] = $endDate->format('H:i');
                }
            }
        }
        if($allDay) {
            $startTime = '00:00';
            $endTime = '23:59';
            $endDate = Carbon::parse($end, $timezone);
        }

        $data = [
            'start' => $startTime,
            'end' => $endTime,
            'allDay' => $allDay,
            'view' => $view,
            'resource' => $resource,
            'date' => $startDate,
            'service_date' => $startDate,
            'timezone' => $timezone,
            'start_val' => $startVal,
            'end_val' => $endVal,
            'date_val' => $dateVal,
        ];

        $this->mountAction('admin', ['data' => $data ]);
        $newIndex = max(0, count($this->mountedActions) - 1);
        $this->dispatch('sync-action-modals', id: $this->getId(), newActionNestingIndex: $newIndex);
       

    }

    public ?array $calendarData = null;

    public function adminAction(): Action
    {
        return Action::make('admin')
            ->label('Admin Actions')
            ->icon('heroicon-o-cog-6-tooth')
            ->color('gray')
            ->modalHeading('Add to booking')
            ->modalDescription('Choose what to create')
            ->modalWidth('sm')
            ->mountUsing(function (array $arguments) {
                $this->calendarData = $arguments['data'];
            })
            ->modalFooterActions([
                Action::make('createLocation')
                    ->label('New Location')
                    ->color('primary')
                    ->icon('heroicon-o-map-pin')
                    ->action(function () {
                        $startDate = \Carbon\Carbon::parse($this->calendarData['start'])->format('Y-m-d');
                        $startVal = $this->calendarData['start_val'];
                        $endVal = $this->calendarData['end_val'];
                        $dateVal = $this->calendarData['date_val'];
                        if ($this->calendarData['allDay']) {
                            $startTime = '00:00';
                            $endTime = '23:59';
                        } else {
                            $startTime = \Carbon\Carbon::parse($this->calendarData['start'])->format('H:i');
                            $endTime = \Carbon\Carbon::parse($this->calendarData['end'])->format('H:i');
                        }
                        if ($endTime === $startTime) {
                          //  $endTime = \Carbon\Carbon::parse($startTime)->addHour()->format('H:i');
                            $startDate  = \Carbon\Carbon::parse($dateVal)->format('Y-m-d');
                            $startTime = \Carbon\Carbon::parse($startVal)->format('H:i');
                            $endTime = \Carbon\Carbon::parse($endVal)->format('H:i');
                        }
                        $data = [
                            'date' => $startDate,
                            'start' => $startTime,
                            'end' => $endTime,
                            'service_date' => $startDate,
                            'start_time' => $startTime,
                            'end_time' => $endTime,
                            'start_val' => $startVal,
                            'end_val' => $endVal,
                            'date_val' => $dateVal,
                        ];
                        $this->replaceMountedAction('createDailyLocation', ['data' => $data]);
                        $newIndex = max(0, count($this->mountedActions) - 1);
                        $this->dispatch('sync-action-modals', id: $this->getId(), newActionNestingIndex: $newIndex);
                    }),
                Action::make('createBooking')
                    ->label('New Booking')
                    ->color('success')
                    ->icon('heroicon-o-calendar-days')
                    ->action(function () {
                        $startDate = \Carbon\Carbon::parse($this->calendarData['start'])->format('Y-m-d');
                        $startVal = $this->calendarData['start_val'];
                        $endVal = $this->calendarData['end_val'];
                        $dateVal = $this->calendarData['date_val'];
                        if ($this->calendarData['allDay']) {
                            $startTime = '00:00';
                            $endTime = '23:59';
                        } else {
                            $startTime = \Carbon\Carbon::parse($this->calendarData['start'])->format('H:i');
                            $endTime = \Carbon\Carbon::parse($this->calendarData['end'])->format('H:i');
                        }
                        if ($endTime === $startTime) {
                          //  $endTime = \Carbon\Carbon::parse($startTime)->addHour()->format('H:i');
                            $startDate  = \Carbon\Carbon::parse($dateVal)->format('Y-m-d');
                            $startTime = \Carbon\Carbon::parse($startVal)->format('H:i');
                            $endTime = \Carbon\Carbon::parse($endVal)->format('H:i');
                        }
                        $data = [
                            'date' => $startDate,
                            'start' => $startTime,
                            'end' => $endTime,
                            'service_date' => $startDate,
                            'start_time' => $startTime,
                            'end_time' => $endTime,
                            'start_val' => $startVal,
                            'end_val' => $endVal,
                            'date_val' => $dateVal,
                        ];
                        $this->replaceMountedAction('create', ['data' => $data]);
                        $newIndex = max(0, count($this->mountedActions) - 1);
                        $this->dispatch('sync-action-modals', id: $this->getId(), newActionNestingIndex: $newIndex);
                    }),
                Action::make('createBlockPeriod')
                    ->label('Block Time')
                    ->color('danger')
                    ->icon('heroicon-o-clock')
                    ->action(function () {
                        $startDate = \Carbon\Carbon::parse($this->calendarData['start'])->format('Y-m-d');
                        $startTime = \Carbon\Carbon::parse($this->calendarData['start'])->format('H:i');
                        $endTime = \Carbon\Carbon::parse($this->calendarData['end'])->format('H:i');
                        $startVal = $this->calendarData['start_val'];
                        $endVal = $this->calendarData['end_val'];
                        $dateVal = $this->calendarData['date_val'];
                        if ($endTime === $startTime) {
                          //  $endTime = \Carbon\Carbon::parse($startTime)->addHour()->format('H:i');
                            $startDate  = \Carbon\Carbon::parse($dateVal)->format('Y-m-d');
                            $startTime = \Carbon\Carbon::parse($startVal)->format('H:i');
                            $endTime = \Carbon\Carbon::parse($endVal)->format('H:i');
                        }
                        $data = [
                            'date' => $startDate,
                            'start' => $startTime,
                            'end' => $endTime,
                            'service_date' => $startDate,
                            'start_time' => $startTime,
                            'end_time' => $endTime,
                            'start_val' => $startVal,
                            'end_val' => $endVal,
                            'date_val' => $dateVal,
                        ];
                        logger()->info('BookingCalendarWidget: BLOCK PERIOD DATA', $data);
                        $this->replaceMountedAction('createServicePeriod', ['data' => $data]);
                        $newIndex = max(0, count($this->mountedActions) - 1);
                        $this->dispatch('sync-action-modals', id: $this->getId(), newActionNestingIndex: $newIndex);
                    }),
            ]);
    }

    public function createDailyLocationAction(): Action
    {
        return Action::make('createDailyLocation')
            ->label('Create Location')
            ->icon('heroicon-o-map-pin')
            ->color('primary')
            ->modalHeading('Create Daily Location')
            ->modalWidth('md')
            ->model(DailyLocation::class)
            ->schema($this->getFormLocation())
            ->fillForm(function (array $arguments) {
                $data = $arguments['data'] ?? [];
                return [
                    'date' => $data['date_val'] ?? $data['service_date'] ?? $data['date'] ?? now()->format('Y-m-d'),
                    'created_by' => Auth::id(),
                ];
            })
            ->action(function (array $data) {
                $data['created_by'] = Auth::id();
                
                DailyLocation::updateOrCreate(
                    ['date' => $data['date'], 'service_user_id' => $data['service_user_id']],
                    $data
                );
                
                $this->refreshRecords();
                
                \Filament\Notifications\Notification::make()
                    ->title('Location saved successfully')
                    ->success()
                    ->send();
            });
    }

    public function createServicePeriodAction(): Action
    {
        return Action::make('createServicePeriod')
            ->label('Create Service Period')
            ->icon('heroicon-o-map-pin')
            ->color('primary')
            ->modalHeading('Create Service Period')
            ->modalWidth('md')
            ->model(BookingServicePeriod::class)
            ->schema($this->getFormPeriod())
            ->fillForm(function (array $arguments) {
                $data = $arguments['data'] ?? [];
                return [
                    'service_date' => $data['date_val'] ?? $data['service_date'] ?? $data['date'],
                    'service_user_id' => $data['service_user_id'] ?? null,
                    'start_time' => $data['start_val'] ?? $data['start_time'] ?? $data['start'],
                    'end_time' => $data['end_val'] ?? $data['end_time'] ?? $data['end'],
                    'created_by' => Auth::id(),
                    'service_location' => $data['service_location'] ?? '',
                    'period_type' => $data['period_type'] ?? 'unavailable',
                ];
            })
            ->action(function (array $data) {
                $data['created_by'] = Auth::id();
                
                BookingServicePeriod::updateOrCreate(
                    [
                        'service_date' => $data['service_date'],
                        'service_user_id' => $data['service_user_id'],
                        'start_time' => $data['start_time'],
                        'end_time' => $data['end_time'],
                    ],
                    $data
                );
                
                $this->refreshRecords();
                
                \Filament\Notifications\Notification::make()
                    ->title('Period saved successfully')
                    ->success()
                    ->send();
            });
    }

    public function createBookingAction(): Action
    {
        return Action::make('create')
            ->label('Create Booking')
            ->icon('heroicon-o-plus')
            ->color('primary')
            ->modalHeading('Create Booking')
            ->modalWidth('lg')
            ->model(BookingServicePeriod::class)
            ->fillForm(function (array $arguments) {
                $data = $arguments['data'] ?? [];
                return [
                    'service_date' => $data['service_date'] ?? now()->format('Y-m-d'),
                    'service_user_id' => $data['service_user_id'] ?? null,
                    'start_time' => $data['start_time'] ?? null,
                    'end_time' => $data['end_time'] ?? null,
                    'created_by' => Auth::id(),
                    'service_location' => $data['service_location'] ?? '',
                    'period_type' => $data['period_type'] ?? 'available',
                ];
            })
            ->schema($this->getFormSchema())
            ->action(function (array $data) {
                $data['created_by'] = Auth::id();

                BookingServicePeriod::updateOrCreate(
                    [
                        'service_date' => $data['service_date'],
                        'service_user_id' => $data['service_user_id'],
                        'start_time' => $data['start_time'],
                        'end_time' => $data['end_time'],
                    ],
                    $data
                );

                $this->refreshRecords();

                \Filament\Notifications\Notification::make()
                    ->title('Booking created successfully')
                    ->success()
                    ->send();
            });
    }

    public function onEventClick(array $event): void
    {
        // Skip clicks on all-day location events to prevent 404 errors
        if (isset($event['allDay']) && $event['allDay'] === true) {
            return;
        }

        // Skip location events (they have IDs starting with 'location-')
        if (isset($event['id']) && str_starts_with($event['id'], 'location-')) {
            return;
        }

        if ($this->getModel()) {
            $this->record = $this->resolveRecord($event['id']);
        }
        if ($this->getModelAlt()) {
            $this->record = $this->resolveRecord($event['id']);
        }
        if (! $this->record) {
            return;
        }

        $this->eventRecord = $this->record;
        $this->record->load('items');
        $this->recordId = $this->record->id;

        $booking = $this->record;
        $user = Auth::user();

        $canEdit = $user->id == $booking->booking_user_id || $this->isAdmin($user);

        $action = $canEdit ? 'edit' : 'view';

        $payload = $this->record->toArray();
        $payload['service_date'] = $this->record->service_date?->format('Y-m-d') ?? ($payload['service_date'] ?? null);

        $this->mountAction($action, [
            'type' => 'click',
            'event' => $event,
            'data' => $payload,
        ]);
    }


    protected function getDateClickContextMenuActions(): array
    {
        $user = Auth::user();

        if (! $user || ! $this->isAdmin($user)) {
            return [];
        }

        return [
            $this->adminAction(),
        ];
    }

    protected function isAdmin(User $user): bool
    {
        return in_array($user->role, ['admin', 'superadmin']);
    }

    public function getFormPeriod(): array
    {
        return [
                      Select::make('service_user_id')
                ->label('Service User')
                ->relationship('serviceUser', 'name')
                ->required(),
            TextInput::make('service_location')
                ->label('Location')
                ->hidden()
                ->required(),
            DatePicker::make('service_date')
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
                ->hidden()
                ->required(),
        ];
    }
        
    public function getFormLocation(): array
    {
        return [

            DatePicker::make('date')
                ->label('Date')
                ->required()
                ->native(false),
            Select::make('service_user_id')
                ->label('Service User')
                ->relationship('serviceUser', 'name')
                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                    $date = $get('date');
                    if ($date && $state) {
                        $existingLocation = DailyLocation::where('date', $date)
                            ->where('service_user_id', $state)
                            ->value('location');
                        if ($existingLocation) {
                            $set('location', $existingLocation);
                        }
                    }
                })
                ->required(),
            TextInput::make('location')->required(),
            Hidden::make('created_by'),
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


    protected function getDefaultFormData(array $seed = []): array
    {
        return array_replace([
            'number' => $this->generateNumber(),
            'booking_client_id' => null,
            'service_id' => null,
            'booking_location_id' => null,
            'service_user_id' => null,
            'service_date' => null,
            'start_time' => null,
            'end_time' => null,
            'status' => BookingStatus::Booked->value,
            'total_price' => null,
            'notes' => null,
            'service_note' => null,
            'items' => [],
        ], $seed);
    }

        protected function generateNumber(): string
    {
        return 'BK-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
    }

    public function getEvents(FetchInfo $info): Collection|array|Builder
    {
        $start = $info->start->toMutable()->startOfDay();
        $end = $info->end->toMutable()->endOfDay();

        $blockingPeriods = BookingServicePeriod::query()
            ->where('period_type', '=', 'unavailable')
            ->get();

        $blockingEvents = $blockingPeriods->map(fn (BookingServicePeriod $blockingPeriod) => $blockingPeriod->toCalendarEvent())->toArray();

        $bookings = Booking::query()
            ->with(['client', 'service', 'serviceUser', 'bookingUser', 'location'])
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('service_date', [$start->toDateString(), $end->toDateString()])
                    ->when(
                        Schema::hasColumn('booking_bookings', 'starts_at'),
                        fn ($q) => $q->orWhereBetween('starts_at', [$start, $end]),
                    );
            })
            ->where('is_active', true)
            ->get();

        // Transform bookings to calendar events
        $bookingEvents = $bookings->map(fn (Booking $booking) => $booking->toCalendarEvent())->toArray();

        // Also include DailyLocation entries as all-day events on calendar
        $dailyLocations = DailyLocation::query()
            ->whereBetween('date', [$start, $end])
            ->with(['serviceUser'])
            ->get();

        $locationEvents = $dailyLocations->map(function (DailyLocation $loc) {
        $title = $loc->location ?: ($loc->serviceUser?->name ?? 'Location');

            return [
                'id' => 'location-'.$loc->id,
                'title' => $title,
                'start' => $loc->date?->toDateString(),
                'allDay' => true,
                'backgroundColor' => '#f3f4f6',
                'borderColor' => 'transparent',
                'textColor' => '#111827',
                'extendedProps' => [
                    'is_location' => true,
                    'daily_location_id' => $loc->id,
                    'service_user_id' => $loc->service_user_id,
                    'location' => $loc->location,
                ],
            ];
        })->toArray();

        return collect(array_merge($bookingEvents, $locationEvents, $blockingEvents));
    }

    public function fetchEvents(array $info): array
    {
        // FullCalendar may send `start`/`end` without `startStr`/`endStr`; ensure both for FetchInfo VO.
        $info['startStr'] ??= $info['start'] ?? null;
        $info['endStr'] ??= $info['end'] ?? null;

        if (! ($info['startStr'] && $info['endStr'])) {
            return [];
        }

        return $this->getEventsJs($info);
    }

    public function getDateSelectContextMenuActions(): array
    {
        return [
            $this->adminAction(),
        ];
    }

}
