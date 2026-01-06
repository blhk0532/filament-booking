<?php

namespace Adultdate\FilamentBooking\Filament\Clusters\Services\Resources\Bookings\Widgets;

use Adultdate\FilamentBooking\Concerns\CanRefreshCalendar;
use Adultdate\FilamentBooking\Concerns\HasOptions;
use Adultdate\FilamentBooking\Concerns\HasSchema;
use Adultdate\FilamentBooking\Concerns\InteractsWithCalendar;
use Adultdate\FilamentBooking\Concerns\InteractsWithEventRecord;
use Adultdate\FilamentBooking\Contracts\HasCalendar;
use Adultdate\FilamentBooking\Enums\BookingStatus;
use Adultdate\FilamentBooking\Filament\Widgets\Concerns\CanBeConfigured;
use Adultdate\FilamentBooking\Filament\Widgets\Concerns\InteractsWithEvents;
use Adultdate\FilamentBooking\Filament\Widgets\Concerns\InteractsWithRawJS;
use Adultdate\FilamentBooking\Filament\Widgets\Concerns\InteractsWithRecords;
use Adultdate\FilamentBooking\Filament\Widgets\FullCalendarWidget;
use Adultdate\FilamentBooking\Models\Booking\Booking;
use Adultdate\FilamentBooking\Models\Booking\BookingLocation;
use Adultdate\FilamentBooking\Models\Booking\Client;
use Adultdate\FilamentBooking\Models\Booking\DailyLocation;
use Adultdate\FilamentBooking\Models\Booking\Service;
use Adultdate\FilamentBooking\Models\BookingServicePeriod;
use Adultdate\FilamentBooking\Models\CalendarSettings;
use Adultdate\FilamentBooking\ValueObjects\FetchInfo;
use App\Models\User;
use App\UserRole;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class BookingCalendar extends FullCalendarWidget implements HasCalendar
{
    public ?int $recordId = null;

    public ?array $lastMountedData = null;

    public Model | int | string | null $record;

    public ?Model $eventRecord = null;

    protected $settings;

    protected $listeners = ['refreshCalendar' => 'refreshCalendar'];

    //    protected bool $eventDragEnabled = true;
    //    protected bool $eventResizeEnabled = true;
    //    protected bool $dateClickEnabled = true;
    //    protected bool $dateSelectEnabled = true;

    protected static ?int $sort = -1;

    use CanBeConfigured, CanRefreshCalendar, HasOptions, HasSchema, InteractsWithCalendar, InteractsWithEventRecord, InteractsWithEvents, InteractsWithPageFilters, InteractsWithRawJS, InteractsWithRecords {
        // Prefer the contract-compatible refreshRecords (chainable) from CanRefreshCalendar
        CanRefreshCalendar::refreshRecords insteadof InteractsWithEvents;

        // Keep the frontend-only refresh available under an alias if needed
        InteractsWithEvents::refreshRecords as refreshRecordsFrontend;

        // Resolve __get collision: prefer InteractsWithPageFilters for pageFilters access
        InteractsWithPageFilters::__get insteadof InteractsWithCalendar;

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

    public function getHeading(): string | Htmlable
    {
        return 'Calenar';
    }

    public function getFooterActions(): array
    {
        return [
            Action::make('create')
                ->requiresConfirmation(true)
                ->action(function (array $arguments) {
                    dd('Admin action called', $arguments);
                }),
        ];
    }

    public function editServicePeriodAction(): Action
    {
        return Action::make('editServicePeriod')
            ->label('Edit Service Period')
            ->icon('heroicon-o-clock')
            ->color('primary')
            ->modalHeading('Edit Service Period')
            ->modalWidth('md')
            ->model(BookingServicePeriod::class)
            ->schema($this->getFormPeriod())
            ->fillForm(function (array $arguments) {
                $data = $arguments['data'] ?? [];
                $serviceUserId = $this->getSelectedServiceUserId();

                return [
                    'service_date' => $data['service_date'] ?? null,
                    'service_user_id' => $data['service_user_id'] ?? $serviceUserId,
                    'start_time' => $data['start_time'] ?? null,
                    'end_time' => $data['end_time'] ?? null,
                    'service_location' => $data['service_location'] ?? '',
                    'period_type' => $data['period_type'] ?? 'unavailable',
                ];
            })
            ->action(function (array $data, array $arguments) {
                $id = $arguments['data']['id'] ?? null;
                if ($id) {
                    BookingServicePeriod::whereKey($id)->update($data);
                }
                $this->refreshRecords();
                \Filament\Notifications\Notification::make()
                    ->title('Period updated successfully')
                    ->success()
                    ->send();
            })
            ->modalSubmitActionLabel('Update')
            ->modalCancelActionLabel('Cancel')
            ->extraModalFooterActions([
                \Filament\Actions\Action::make('deleteFromModal')
                    ->label('Delete')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Delete Service Period')
                    ->modalDescription('Are you sure? This action cannot be undone.')
                    ->modalSubmitActionLabel('Delete')
                    ->action(function (array $arguments) {
                        $id = $arguments['data']['id'] ?? null;
                        if ($id) {
                            BookingServicePeriod::whereKey($id)->delete();
                        }
                        $this->refreshRecords();
                        \Filament\Notifications\Notification::make()
                            ->title('Period deleted successfully')
                            ->success()
                            ->send();
                    }),
            ]);
    }

    public function deleteServicePeriodAction(): Action
    {
        return Action::make('deleteServicePeriod')
            ->label('Delete Period')
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Delete Service Period')
            ->modalDescription('Are you sure you want to delete this service period? This action cannot be undone.')
            ->action(function (array $arguments) {
                $id = $arguments['data']['id'] ?? null;
                if ($id) {
                    BookingServicePeriod::whereKey($id)->delete();
                }
                $this->refreshRecords();
                \Filament\Notifications\Notification::make()
                    ->title('Period deleted successfully')
                    ->success()
                    ->send();
            });
    }

    public function getModelAlt(): string
    {
        return DailyLocation::class;
    }

    public function getModelPeriod(): string
    {
        return BookingServicePeriod::class;
    }

    public function getModel(): string
    {
        return $this->model instanceof Model ? $this->model : Booking::class;
    }

    public function getEventModel(): string
    {
        return $this->model instanceof Model ? $this->getModel() : Booking::class;
    }

    public function getEventRecord(): ?Model
    {
        return $this->record instanceof Model ? $this->record : null;
    }

    protected function getEloquentQuery(): Builder
    {
        return $this->getModel()::query();
    }

    protected int | string | array $columnSpan = 'full';

    public function config(): array
    {
        $this->settings = CalendarSettings::where('user_id', Auth::id())->first();

        $openingStart = $this->settings?->opening_hour_start?->format('H:i:s') ?? '07:00:00';
        $openingEnd = $this->settings?->opening_hour_end?->format('H:i:s') ?? '21:00:00';

        return [
            'initialView' => 'timeGridWeek',
            // Start week on Monday (0 = Sunday, 1 = Monday)
            'firstDay' => 1,
            'dayHeaderFormat' => [
                'weekday' => 'short',
                'day' => 'numeric',
            ],
            'headerToolbar' => [
                'start' => 'prev,today,next',
                'center' => 'title',
                'end' => 'dayGridMonth,timeGridWeek,timeGridDay',
            ],
            'nowIndicator' => true,
            'selectable' => true,
            'dateClick' => true,
            'eventClick' => true,
            'onEventDrop' => 'onEventDrop',
            'timeZone' => 'Europe/Stockholm',
            'now' => now()->setTimezone('Europe/Stockholm')->addHour()->toISOString(),
            'slotMinTime' => $openingStart ? $openingStart : '08:00:00',
            'slotMaxTime' => $openingEnd ? $openingEnd : '18:00:00',
            'views' => [
                'timeGridDay' => [
                    'slotMinTime' => $openingStart ? $openingStart : '08:00:00',
                    'slotMaxTime' => $openingEnd ? $openingEnd : '18:00:00',
                ],
                'timeGridWeek' => [
                    'slotMinTime' => $openingStart ? $openingStart : '08:00:00',
                    'slotMaxTime' => $openingEnd ? $openingEnd : '18:00:00',
                ],
                'timeGridMonth' => [
                    'slotMinTime' => $openingStart ? $openingStart : '08:00:00',
                    'slotMaxTime' => $openingEnd ? $openingEnd : '18:00:00',
                ],
            ],
        ];
    }

    public function onDateClick(string $date, bool $allDay, ?array $view, ?array $resource): void
    {
        $startDate = \Carbon\Carbon::parse($date);

        // In dayGridMonth view, clicking on an empty day should create a location
        if ($view && isset($view['type']) && $view['type'] === 'dayGridMonth') {
            $this->mountAction('createDailyLocation', [
                'date' => $startDate->format('Y-m-d'),
            ]);

            return;
        }

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
            logger()->info('BookingCalendarWidget: ALL-DAY CLICK DETECTED!', ['dataVal' => $dateVal]);

            $this->mountAction('createDailyLocation', [
                'date' => $dateVal->format('Y-m-d'),

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
        if ($allDay) {
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

        $this->mountAction('admin', ['data' => $data]);
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
            ->modalHeading('Skapa booking & hantera schema')
            ->modalDescription('Choose what to create')
            ->modalWidth('sm')
            ->mountUsing(function (array $arguments) {
                $this->calendarData = $arguments['data'];
            })
            ->modalFooterActions([

                Action::make('createBooking')
                    ->label('Bokning')
                    ->color('success')
                    ->icon('heroicon-o-calendar-days')
                    ->action(function () {
                        $startDate = \Carbon\Carbon::parse($this->calendarData['start'])->format('Y-m-d');
                        $startVal = $this->calendarData['start_val'];
                        $endVal = $this->calendarData['end_val'];
                        $dateVal = $this->calendarData['date_val'];
                        $timeStamp = time();
                        $dateStamp = date('Ymd', $timeStamp);
                        $startStamp = date('Ymd', strtotime($startDate));
                        $bookingNumber = 'NDS-' . $startStamp . '-' . Str::upper(Str::substr(Auth::user()->name, 0, 3)) . '-' . $dateStamp . '-Bk-' . $timeStamp . '-S1';
                        if ($this->calendarData['allDay']) {
                            $startTime = '00:00';
                            $endTime = '23:59';
                        } else {
                            $startTime = \Carbon\Carbon::parse($this->calendarData['start_val'])->format('H:i');
                            $endTime = \Carbon\Carbon::parse($this->calendarData['end_val'])->format('H:i');
                        }
                        if ($endTime === $startTime) {
                            $startDate = \Carbon\Carbon::parse($dateVal)->format('Y-m-d');
                            $startTime = \Carbon\Carbon::parse($startVal)->format('H:i');
                            $endTime = \Carbon\Carbon::parse($endVal)->format('H:i');
                        }
                        $data = ['number' => $bookingNumber, 'notes' => '', 'service_user_id' => null, 'booking_client_id' => null, 'date' => $startDate, 'start' => $startTime, 'end' => $endTime, 'service_date' => $startDate, 'start_time' => $startTime, 'end_time' => $endTime, 'start_val' => $startVal, 'end_val' => $endVal, 'date_val' => $dateVal];
                        logger()->info('BookingCalendarWidget: B BOOK DATA', $data);
                        $this->replaceMountedAction('create', ['data' => $data]);
                        $newIndex = max(0, count($this->mountedActions) - 1);
                        $this->dispatch('sync-action-modals', id: $this->getId(), newActionNestingIndex: $newIndex);
                    }),

                Action::make('createLocation')
                    ->label('Schema')
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
                            $startDate = \Carbon\Carbon::parse($dateVal)->format('Y-m-d');
                            $startTime = \Carbon\Carbon::parse($startVal)->format('H:i');
                            $endTime = \Carbon\Carbon::parse($endVal)->format('H:i');
                        }
                        $data = ['date' => $startDate, 'start' => $startTime, 'end' => $endTime, 'service_date' => $startDate, 'start_time' => $startTime, 'end_time' => $endTime, 'start_val' => $startVal, 'end_val' => $endVal, 'date_val' => $dateVal];
                        logger()->info('BookingCalendarWidget: LOCATION DATA', $data);
                        $this->replaceMountedAction('createDailyLocation', ['data' => $data]);
                        $newIndex = max(0, count($this->mountedActions) - 1);
                        $this->dispatch('sync-action-modals', id: $this->getId(), newActionNestingIndex: $newIndex);
                    }),

                Action::make('createBlockPeriod')
                    ->label('')
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
                            $startDate = \Carbon\Carbon::parse($dateVal)->format('Y-m-d');
                            $startTime = \Carbon\Carbon::parse($startVal)->format('H:i');
                            $endTime = \Carbon\Carbon::parse($endVal)->format('H:i');
                        }
                        $data = ['date' => $startDate, 'start' => $startTime, 'end' => $endTime, 'service_date' => $startDate, 'start_time' => $startTime, 'end_time' => $endTime, 'start_val' => $startVal, 'end_val' => $endVal, 'date_val' => $dateVal];
                        logger()->info('BookingCalendarWidget: BLOCK PERIOD DATA', $data);
                        $this->replaceMountedAction('createServicePeriod', ['data' => $data]);
                        $newIndex = max(0, count($this->mountedActions) - 1);
                        $this->dispatch('sync-action-modals', ['id' => $this->getId(), 'newActionNestingIndex' => $newIndex]);
                    }),

                Action::make('close')
                    ->label('')
                    ->color('gray')
                    ->icon('heroicon-o-x-circle')
                    ->close(true)
                    ->action(function () {}),

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
                $serviceUserId = $this->getSelectedServiceUserId();

                return [
                    'date' => $data['date_val'] ?? $data['service_date'] ?? $data['date'] ?? now()->format('Y-m-d'),
                    'service_user_id' => $data['service_user_id'] ?? $serviceUserId,
                    'created_by' => Auth::id(),
                ];
            })
            ->action(function (array $data) {
                $data['created_by'] = Auth::id();
                DailyLocation::updateOrCreate(['date' => $data['date'], 'service_user_id' => $data['service_user_id']], $data);
                $this->refreshRecords();
                \Filament\Notifications\Notification::make()
                    ->title('Location saved successfully')
                    ->success()
                    ->send();
            });
    }

    public function editDailyLocationAction(): Action
    {
        return Action::make('editDailyLocation')
            ->label('Edit Location')
            ->icon('heroicon-o-map-pin')
            ->color('primary')
            ->modalHeading('Edit Daily Location')
            ->modalWidth('md')
            ->model(DailyLocation::class)
            ->schema($this->getFormLocation())
            ->fillForm(function (array $arguments) {
                $data = $arguments['data'] ?? [];
                $serviceUserId = $this->getSelectedServiceUserId();

                return [
                    'date' => $data['date'] ?? now()->format('Y-m-d'),
                    'service_user_id' => $data['service_user_id'] ?? $serviceUserId,
                    'location' => $data['location'] ?? null,
                    'created_by' => Auth::id(),
                ];
            })
            ->action(function (array $data, array $arguments) {
                $id = $arguments['data']['id'] ?? null;
                if ($id) {
                    DailyLocation::whereKey($id)->update($data);
                }
                $this->refreshRecords();
                \Filament\Notifications\Notification::make()
                    ->title('Location updated successfully')
                    ->success()
                    ->send();
            })
            ->modalSubmitActionLabel('Update')
            ->extraModalFooterActions(function (array $arguments) {
                $id = $arguments['data']['id'] ?? null;
                if (! $id) {
                    return [];
                }

                return [
                    Action::make('deleteLocation')
                        ->label('Delete')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function () use ($id) {
                            DailyLocation::whereKey($id)->delete();
                            $this->refreshRecords();
                            \Filament\Notifications\Notification::make()
                                ->title('Location deleted successfully')
                                ->success()
                                ->send();
                        }),
                ];
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
                $serviceUserId = $this->getSelectedServiceUserId();

                return [
                    'service_date' => $data['date_val'] ?? $data['service_date'] ?? $data['date'],
                    'service_user_id' => $data['service_user_id'] ?? $serviceUserId,
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
            ->fillForm(function (array $arguments) {
                $data = $arguments['data'] ?? [];
                $serviceUserId = $this->getSelectedServiceUserId();

                return [
                    'number' => $this->generateNumber(),
                    'service_date' => $data['service_date'] ?? now()->format('Y-m-d'),
                    'service_user_id' => $data['service_user_id'] ?? $serviceUserId,
                    'start_time' => $data['start_time'] ?? null,
                    'end_time' => $data['end_time'] ?? null,
                    'status' => BookingStatus::Booked->value,
                ];
            })
            ->schema($this->getFormSchema())
            ->action(function (array $data) {
                $booking = Booking::create($data);

                if (isset($data['items']) && is_array($data['items'])) {
                    foreach ($data['items'] as $item) {
                        if (isset($item['booking_service_id'])) {
                            $booking->items()->create([
                                'booking_service_id' => $item['booking_service_id'],
                                'qty' => $item['qty'] ?? 1,
                                'unit_price' => $item['unit_price'] ?? 0,
                            ]);
                        }
                    }
                }

                $booking->updateTotalPrice();
                $this->refreshRecords();
                \Filament\Notifications\Notification::make()
                    ->title('Booking created successfully')
                    ->success()
                    ->send();
            });
    }

    public function manageBlockAction(): Action
    {
        $widget = $this;

        return Action::make('manageBlock')
            ->label('Manage Block')
            ->icon('heroicon-o-cog')
            ->color('gray')
            ->modalWidth('sm')
            ->modalHeading('Manage Service Period')
            ->modalDescription('Choose an action for this block period')
            ->modalFooterActions([
                Action::make('edit')
                    ->label('Edit')
                    ->color('primary')
                    ->icon('heroicon-o-pencil')
                    ->cancelParentActions()
                    ->action(function () use ($widget) {
                        $widget->mountAction('editBlock');
                    }),
                Action::make('delete')
                    ->label('Delete')
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->modalHeading('Delete Service Period')
                    ->modalDescription('Are you sure you want to delete this service period? This action cannot be undone.')
                    ->action(function () use ($widget) {
                        $widget->record->delete();
                        $widget->refreshRecords();
                        \Filament\Notifications\Notification::make()
                            ->title('Service period deleted successfully')
                            ->success()
                            ->send();
                    }),
            ]);
    }

    public function optionsAction(): Action
    {
        return Action::make('options')
            ->label('Admin Actions')
            ->icon('heroicon-o-cog-6-tooth')
            ->color('gray')
            ->modalHeading('Manage Update Booking')
            ->modalDescription('')
            ->modalWidth('sm')
            ->model(Booking::class)
            ->mountUsing(function (array $arguments) {
                $this->calendarData = $arguments['data'];
            })
            ->modalFooterActions([

                Action::make('confirm')
                    ->label('Confirm')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->action(function () {
                        $data = $arguments['data'] ?? [];
                        $this->replaceMountedAction('confirmBooking', ['data' => $data]);
                        $newIndex = max(0, count($this->mountedActions) - 1);
                        $this->dispatch('sync-action-modals', ['id' => $this->getId(), 'newActionNestingIndex' => $newIndex]);
                    }),
                Action::make('edit')
                    ->label('Update')
                    ->color('warning')
                    ->icon('heroicon-o-calendar')
                    ->requiresConfirmation(false)
                    ->action(function () {
                        $data = $arguments['data'] ?? [];
                        $this->replaceMountedAction('edit', ['data' => $data]);
                        $newIndex = max(0, count($this->mountedActions) - 1);
                        $this->dispatch('sync-action-modals', id: $this->getId(), newActionNestingIndex: $newIndex);
                    }),

                Action::make('delete')
                    ->label('Radera')
                    ->color('danger')
                    ->requiresConfirmation(true)
                    ->icon('heroicon-o-trash')
                    ->action(function () {
                        $data = $arguments['data'] ?? [];
                        $this->replaceMountedAction('delete', ['data' => $data]);
                        $newIndex = max(0, count($this->mountedActions) - 1);
                        $this->dispatch('sync-action-modals', id: $this->getId(), newActionNestingIndex: $newIndex);
                    }),

            ]);
    }

    public function locationOptionsAction(): Action
    {
        return Action::make('locationOptions')
            ->label('Location options')
            ->icon('heroicon-o-map-pin')
            ->color('gray')
            ->modalHeading('Edit location')
            ->modalWidth('sm')
            ->model(DailyLocation::class)
            ->mountUsing(function (array $arguments) {
                $this->calendarData = $arguments['data'];
            })
            ->modalFooterActions([
                Action::make('edit')
                    ->label('Edit')
                    ->color('primary')
                    ->icon('heroicon-o-pencil-square')
                    ->action(function () {
                        $data = $arguments['data'] ?? [];
                        $this->replaceMountedAction('editDailyLocation', ['data' => $data]);
                        $newIndex = max(0, count($this->mountedActions) - 1);
                        $this->dispatch('sync-action-modals', ['id' => $this->getId(), 'newActionNestingIndex' => $newIndex]);
                    }),
                Action::make('delete')
                    ->label('Delete')
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->action(function () {
                        $data = $arguments['data'] ?? [];
                        $id = $data['id'] ?? null;
                        if ($id) {
                            DailyLocation::whereKey($id)->delete();
                            $this->refreshRecords();
                        }
                        $this->dispatch('sync-action-modals', ['id' => $this->getId(), 'newActionNestingIndex' => 0]);
                    }),
                Action::make('close')
                    ->label('Close')
                    ->color('gray')
                    ->close(true)
                    ->icon('heroicon-o-x-circle'),
            ]);
    }

    public function periodOptionsAction(): Action
    {
        return Action::make('periodOptions')
            ->label('Period options')
            ->icon('heroicon-o-clock')
            ->color('gray')
            ->modalHeading('Edit period')
            ->modalWidth('sm')
            ->model(BookingServicePeriod::class)
            ->mountUsing(function (array $arguments) {
                $this->calendarData = $arguments['data'];
            })
            ->modalFooterActions([
                Action::make('edit')
                    ->label('Edit')
                    ->color('primary')
                    ->icon('heroicon-o-pencil-square')
                    ->action(function () {
                        $data = $arguments['data'] ?? [];
                        $this->replaceMountedAction('editServicePeriod', ['data' => $data]);
                        $newIndex = max(0, count($this->mountedActions) - 1);
                        $this->dispatch('sync-action-modals', ['id' => $this->getId(), 'newActionNestingIndex' => $newIndex]);
                    }),
                Action::make('delete')
                    ->label('Delete')
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->action(function () {
                        $data = $arguments['data'] ?? [];
                        $id = $data['id'] ?? null;
                        if ($id) {
                            BookingServicePeriod::whereKey($id)->delete();
                            $this->refreshRecords();
                        }
                        $this->dispatch('sync-action-modals', ['id' => $this->getId(), 'newActionNestingIndex' => 0]);
                    }),
                Action::make('close')
                    ->label('Close')
                    ->color('gray')
                    ->close(true)
                    ->icon('heroicon-o-x-circle'),
            ]);
    }

    public function onEventClick($event): void
    {
        logger()->info('zzz: onEventClick', ['events' => $event]);

        $title = $event['title'] ?? null;
        $start = $event['start'] ?? null;
        $end = $event['end'] ?? null;
        $view = $event['view'] ?? null;
        $resource = $event['resource'] ?? null;
        // logger()->info('zzz: onEventClick', ['events' => $start . ' ' . $end . ' ' . ($allDay ? 'allDay' : 'notAllDay')]);

        $allDay = (bool) ($event['allDay']);

        logger()->info('BookingCalendarWidget CALENDAR WAS CLICKED', [
            'title' => $title,
            'start' => $start,
            'end' => $end,
            'allDay' => $allDay,
            'view' => $view,
            'resource' => $resource,
        ]);

        $type = data_get($event, 'extendedProps.type', 'booking');

        logger()->info('BookingCalendarWidget: Event type detected', ['type' => $type]);

        switch ($type) {
            case 'blocking':
                $recId = $event['extendedProps']['booking_id'] ?? null;
                logger()->info('BookingCalendarWidget: Blocking period click', ['recId' => $recId]);

                if (! $recId) {
                    logger()->error('BookingCalendarWidget: No record ID found for blocking period');

                    return;
                }

                try {
                    $this->model = BookingServicePeriod::class;

                    // Directly query the record instead of using resolveRecord
                    $this->record = BookingServicePeriod::find($recId);

                    if (! $this->record) {
                        logger()->error('BookingCalendarWidget: Record not found', ['id' => $recId]);
                        \Filament\Notifications\Notification::make()
                            ->title('Period not found')
                            ->danger()
                            ->send();

                        return;
                    }

                    if ($this->record instanceof Model) {
                        $this->eventRecord = $this->record;
                        $this->recordId = $this->record->id;
                        $payload = $this->record->toArray();
                    } else {
                        logger()->error('BookingCalendarWidget: Record is not a valid Model instance', ['record' => $this->record]);
                        \Filament\Notifications\Notification::make()
                            ->title('Invalid record type')
                            ->danger()
                            ->send();

                        return;
                    }

                    $user = Auth::user();
                    $canEdit = in_array($user->role, [\App\UserRole::ADMIN, \App\UserRole::SUPER_ADMIN], true);

                    logger()->info('BookingCalendarWidget: Mounting editServicePeriod', [
                        'canEdit' => $canEdit,
                        'userRole' => $user->role->value,
                        'recordId' => $this->record->id,
                    ]);

                    if ($canEdit) {
                        $this->mountAction('editServicePeriod', [
                            'data' => $payload,
                        ]);
                        // Store the payload for delete action
                        $this->lastMountedData = $payload;
                    } else {
                        logger()->info('BookingCalendarWidget: User does not have permission to edit blocking period', [
                            'userRole' => $user->role->value,
                        ]);
                        \Filament\Notifications\Notification::make()
                            ->title('Permission denied')
                            ->body('You do not have permission to edit blocking periods')
                            ->warning()
                            ->send();
                    }
                } catch (\Exception $e) {
                    logger()->error('BookingCalendarWidget: Exception in blocking case', [
                        'message' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                    \Filament\Notifications\Notification::make()
                        ->title('Error loading period')
                        ->body($e->getMessage())
                        ->danger()
                        ->send();
                }

                break;

            case 'location':
                if ($allDay) {
                    $recId = $event['extendedProps']['daily_location_id'] ?? null;

                    try {
                        $this->model = DailyLocation::class;
                        $this->record = DailyLocation::find($recId);
                        if (! $this->record) {
                            throw new \Exception("Location record not found: {$recId}");
                        }
                        if ($this->record instanceof Model) {
                            $this->eventRecord = $this->record;
                            $this->recordId = $this->record->id;
                            $payload = $this->record->toArray();
                        }
                        $user = Auth::user();
                        $canEdit = in_array($user->role, [\App\UserRole::ADMIN, \App\UserRole::SUPER_ADMIN], true);
                        \Illuminate\Support\Facades\Log::info('BookingCalendarWidget: Location click', [
                            'canEdit' => $canEdit,
                            'userRole' => $user->role->value ?? $user->role,
                            'recordId' => $recId,
                        ]);
                        $action = $canEdit ? 'editDailyLocation' : '';
                        if ($action) {
                            $this->mountAction($action, [
                                'data' => $payload,
                            ]);
                        }
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('BookingCalendarWidget: Location error', [
                            'error' => $e->getMessage(),
                            'recId' => $recId,
                        ]);
                        \Filament\Notifications\Notification::make()
                            ->title('Error loading location')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }

                break;

            case 'booking':
            default:
                if (! $allDay) {
                    $recId = $event['id'] ?? null;

                    try {
                        $this->model = Booking::class;
                        $this->record = Booking::find($recId);
                        if (! $this->record) {
                            throw new \Exception("Booking record not found: {$recId}");
                        }
                        if ($this->record instanceof Model) {
                            $this->eventRecord = $this->record;
                            $this->record->load('items');
                            $this->recordId = $this->record->id;
                            $payload = $this->record->toArray();
                        }
                        $payload['service_date'] = $this->record->service_date?->format('Y-m-d') ?? ($payload['service_date'] ?? null);
                        $booking = $this->record;
                        $user = Auth::user();
                        $canEdit = $user->id == $booking->booking_user_id || in_array($user->role, [\App\UserRole::ADMIN, \App\UserRole::SUPER_ADMIN], true);
                        \Illuminate\Support\Facades\Log::info('BookingCalendarWidget: Booking click', [
                            'canEdit' => $canEdit,
                            'isBookingOwner' => $user->id == $booking->booking_user_id,
                            'userRole' => $user->role->value ?? $user->role,
                            'recordId' => $recId,
                        ]);
                        $action = $canEdit ? 'options' : '';
                        if ($action) {
                            logger()->info('CLICK $payload:', $payload);
                            $this->mountAction($action, [
                                'data' => $payload,
                            ]);
                        }
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('BookingCalendarWidget: Booking error', [
                            'error' => $e->getMessage(),
                            'recId' => $recId,
                        ]);
                        \Filament\Notifications\Notification::make()
                            ->title('Error loading booking')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                } else {
                    // All-day click for creating location
                    $timezone = config('app.timezone');
                    $startDate = Carbon::parse($start, $timezone);
                    $this->mountAction('createDailyLocation', [
                        'date' => $startDate->format('Y-m-d'),
                        'service_date' => $startDate->format('Y-m-d'),
                        'service_user_id' => $event['extendedProps']['service_user_id'] ?? null,
                        'created_by' => Auth::id(),
                    ]);
                }

                break;
        }
    }

    public function eventDropped(string $eventId, string $startStr, ?string $endStr = null, string $type = 'booking', bool $allDay = false): void
    {
        // Only allow admins to drag and drop
        if (! Auth::check() || ! in_array(Auth::user()->role, [\App\UserRole::ADMIN, \App\UserRole::SUPER_ADMIN])) {
            $this->dispatch('notify', 'error', 'You do not have permission to modify events.');

            return;
        }

        $start = Carbon::parse($startStr, 'Europe/Stockholm');
        $end = $endStr ? Carbon::parse($endStr, 'Europe/Stockholm') : null;

        $serviceDate = $start->toDateString();

        // Validate drag operations based on event type and target
        switch ($type) {
            case 'booking':
            case 'blocking':
                // Timed events cannot be dropped to all-day row
                if ($allDay) {
                    $this->dispatch('notify', 'error', 'Timed events cannot be moved to the all-day row.');

                    return;
                }
                $startTime = $start->format('H:i:s');
                $endTime = $end?->format('H:i:s');

                break;

            case 'location':
                // Location events are always all-day and should stay that way
                if (! $allDay) {
                    $this->dispatch('notify', 'error', 'Location events can only be moved within the all-day row.');

                    return;
                }
                $startTime = null;
                $endTime = null;

                break;

            default:
                $this->dispatch('notify', 'error', 'Unknown event type.');

                return;
        }

        switch ($type) {
            case 'booking':
                /** @var Booking|null $booking */
                $booking = Booking::find($eventId);
                if ($booking) {
                    $booking->update([
                        'service_date' => $serviceDate,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'starts_at' => $start->toIso8601String(),
                        'ends_at' => $end?->toIso8601String(),
                    ]);
                    $this->dispatch('notify', 'success', 'Booking moved successfully.');
                }

                break;

            case 'location':
                /** @var DailyLocation|null $location */
                $location = DailyLocation::find($eventId);
                if ($location) {
                    $location->update([
                        'date' => $serviceDate,
                    ]);
                    $this->dispatch('notify', 'success', 'Location moved successfully.');
                }

                break;

            case 'blocking':
                /** @var BookingServicePeriod|null $blocking */
                $blocking = BookingServicePeriod::find($eventId);
                if ($blocking) {
                    $blocking->update([
                        'service_date' => $serviceDate,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'starts_at' => $start->toIso8601String(),
                        'ends_at' => $end?->toIso8601String(),
                    ]);
                    $this->dispatch('notify', 'success', 'Blocking period moved successfully.');
                }

                break;
        }

        // Refresh the calendar
        $this->refreshCalendar();
    }

    public function onEventDrop($event): void
    {
        $id = data_get($event, 'id');
        $start = data_get($event, 'startStr') ?? data_get($event, 'start');
        $end = data_get($event, 'endStr') ?? data_get($event, 'end');
        $type = data_get($event, 'extendedProps.type') ?? data_get($event, 'type') ?? 'booking';
        $allDay = data_get($event, 'allDay', false);

        if (! $id || ! $start) {
            $this->dispatch('notify', 'error', 'Unable to move event: missing event data.');

            return;
        }

        $this->eventDropped((string) $id, (string) $start, $end ? (string) $end : null, (string) $type, (bool) $allDay);
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
        return $user->role === UserRole::ADMIN || $user->role === UserRole::SUPER_ADMIN;
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
            'booking_user_id' => null,
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
        return 'BK-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
    }

    public function getEvents(FetchInfo $info): Collection | array | Builder
    {
        $start = $info->start->toMutable()->startOfDay();
        $end = $info->end->toMutable()->endOfDay();

        $filters = $this->pageFilters;
        $selectedCalendarId = $filters['booking_calendars'] ?? null;

        $serviceUserId = null;
        if ($selectedCalendarId) {
            $calendar = \App\Models\BookingCalendar::find($selectedCalendarId);
            $serviceUserId = $calendar?->owner_id;
        }

        $blockingPeriods = BookingServicePeriod::query()
            ->when($serviceUserId, fn ($query) => $query->where('service_user_id', $serviceUserId))
            ->where('period_type', '=', 'unavailable')
            ->get();

        $blockingEvents = $blockingPeriods->map(fn (BookingServicePeriod $blockingPeriod) => $blockingPeriod->toCalendarEvent())->toArray();

        $bookings = Booking::query()
            ->with(['client', 'service', 'serviceUser', 'bookingUser', 'location'])
            ->when($serviceUserId, fn ($query) => $query->where('service_user_id', $serviceUserId))
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
            ->when($serviceUserId, fn ($query) => $query->where('service_user_id', $serviceUserId))
            ->whereBetween('date', [$start, $end])
            ->with(['serviceUser'])
            ->get();

        $locationEvents = $dailyLocations->map(function (DailyLocation $loc) {
            $title = $loc->location ?: ($loc->serviceUser?->name ?? 'Location');

            return [
                'id' => $loc->id,
                'title' => $title,
                'eventsType' => 'location',
                'type' => 'location',
                'start' => $loc->date?->toDateString(),
                'number' => 0,
                'allDay' => true,
                'backgroundColor' => '#f3f4f6',
                'borderColor' => 'transparent',
                'textColor' => '#111827',
                'extendedProps' => [
                    'is_location' => true,
                    'type' => 'location',
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

    public function refreshCalendar()
    {
        $this->refreshRecords();
    }

    protected function getSelectedServiceUserId(): ?int
    {
        $filters = $this->pageFilters ?? [];
        $selectedCalendarId = $filters['booking_calendars'] ?? null;

        if ($selectedCalendarId) {
            $calendar = \App\Models\BookingCalendar::find($selectedCalendarId);

            return $calendar?->owner_id;
        }

        return null;
    }

    public function mount(): void
    {
        $this->eventClickEnabled = true;
        $this->dateClickEnabled = true;
        $this->eventDragEnabled = true;
        $this->eventResizeEnabled = true;
        $this->dateSelectEnabled = true;
    }
}
