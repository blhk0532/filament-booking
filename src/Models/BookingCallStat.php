<?php

namespace Adultdate\FilamentBooking\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingCallStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'lead_id',
        'booking_id',
        'outcome',
        'duration',
        'notes',
        'booked_meeting',
        'call_date',
    ];

    protected function casts(): array
    {
        return [
            'call_date' => 'datetime',
            'booked_meeting' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(BookingDataLead::class, 'lead_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(\Adultdate\FilamentBooking\Models\Booking\Booking::class, 'booking_id');
    }
}
