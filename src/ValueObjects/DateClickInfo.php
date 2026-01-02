<?php

namespace Adultdate\FilamentBooking\ValueObjects;

use Carbon\CarbonImmutable;
use Adultdate\FilamentBooking\Contracts\ContextualInfo;
use Adultdate\FilamentBooking\Enums\Context;

use function Adultdate\FilamentBooking\utc_to_user_local_time;

readonly class DateClickInfo implements ContextualInfo
{
    public CarbonImmutable $date;

    public bool $allDay;

    public CalendarView $view;

    protected array $originalData;

    public function __construct(array $data, bool $useFilamentTimezone)
    {
        $this->originalData = $data;

        $this->date = utc_to_user_local_time(
            data_get($data, 'date'),
            data_get($data, 'tzOffset'),
            $useFilamentTimezone
        );

        $this->allDay = data_get($data, 'allDay');

        $this->view = new CalendarView(
            data_get($data, 'view'),
            data_get($data, 'tzOffset'),
            $useFilamentTimezone
        );
    }

    public function getContext(): Context
    {
        return Context::DateClick;
    }
}
