<?php

namespace Adultdate\FilamentBooking\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class BookingServicePeriod extends Model
{
    use HasFactory;

    protected $table = 'booking_service_periods';

    // Prefer explicit fillable to avoid accidental mass-assignment
    protected $fillable = [
        'service_date',
        'service_user_id',
        'service_location',
        'start_time',
        'end_time',
        'period_type',
        'created_by',
    ]; 

    /**
     * Casts
     */
    protected $casts = [
        'service_date' => 'date',
        'start_time' => 'string',
        'end_time' => 'string',
        'id', 
    ];

    /**
     * The user this service period belongs to.
     */
    public function serviceUser(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'service_user_id');
    }

    /**
     * The user who created this period.
     */
    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * The bookings for this period.
     */
    public function items(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\Adultdate\FilamentBooking\Models\Booking\Booking::class, 'service_user_id', 'service_user_id')
            ->where('service_date', $this->service_date);
    }

        public function toCalendarEvent(): array
    {
        $start = null;
        $end = null;

        if ($this->service_date && $this->start_time) {
            $start = $this->service_date->toDateString().'T'.
                str($this->start_time)->padRight(8, ':00');
        } elseif ($this->starts_at) {
            $start = $this->starts_at->toIso8601String();
        }

        if ($this->service_date && $this->end_time) {
            $end = $this->service_date->toDateString().'T'.
                str($this->end_time)->padRight(8, ':00');
        } elseif ($this->ends_at) {
            $end = $this->ends_at->toIso8601String();
        }

        return [
            'id' => $this->id,
            'title' => $this->client?->name ?? 'ⓘ zzz',
            'start' => $start,
            'end' => $end,
            'type' => 'blocking',
            'backgroundColor' => $this->status?->getColor() ?? '#f3f4f6',
            'borderColor' => $this->status?->getColor() ?? 'transparent',
            'extendedProps' => [
                'key' => $this->id,  // Required: Record ID for event resolution
                'booking_id' => $this->id,
                'number' => $this->number,
                'client_name' => $this->client?->name,
                'service_date' => $this->service_date?->format('Y-m-d'),
                'service_name' => $this->service?->name,
                'service_user' => $this->serviceUser?->name,
                'booking_user' => $this->bookingUser?->name,
                'location' => $this->location?->name,
                'displayLocation' => $this->location?->name,
                // Model FQCN used by calendar to select custom event content
                'model' => static::class,
                'status' => $this->status?->value,
                'total_price' => $this->total_price,
                'currency' => $this->currency,
                'notes' => $this->notes,
                'type' => 'blocking',
            ],
        ];
    }


}
