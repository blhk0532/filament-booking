<?php

namespace Adultdate\FilamentBooking\Models\Booking;

use Adultdate\FilamentBooking\Contracts\Eventable;
use Adultdate\FilamentBooking\ValueObjects\CalendarEvent;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static \Illuminate\Database\Eloquent\Builder query()
 */
class DailyLocation extends Model implements Eventable
{
    protected $table = 'booking_daily_locations';

    protected $fillable = [
        'date',
        'service_date',
        'service_user_id',
        'location',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'service_user_id',
        'service_date',
        'location',
    ];

    public function serviceUser()
    {
        return $this->belongsTo(\App\Models\User::class, 'service_user_id');
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function setServiceDateAttribute($value)
    {
        $this->attributes['date'] = $value;
    }

    public function getServiceDateAttribute()
    {
        return $this->attributes['date'] ?? null;
    }

    public function toCalendarEvent(): CalendarEvent
    {
        $title = $this->location ?: ($this->serviceUser?->name ?? 'Location');

        return CalendarEvent::make($this)
            ->title($title)
            ->start($this->date)
            ->end(Carbon::parse($this->date)->endOfDay())
            ->allDay(true)
            ->backgroundColor('#f3f4f6')
            ->textColor('#111827')
            ->extendedProps([
                'is_location' => true,
                'daily_location_id' => $this->id,
                'service_user_id' => $this->service_user_id,
                'location' => $this->location,
            ]);
    }
}
