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
}
