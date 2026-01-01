<?php

namespace Adultdate\FilamentBooking\Models\Booking;

use Illuminate\Database\Eloquent\Model;

class DailyLocation extends Model
{
    protected $table = 'booking_daily_locations';

    protected $fillable = [
        'date',
        'service_user_id',
        'location',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function serviceUser()
    {
        return $this->belongsTo(\App\Models\User::class, 'service_user_id');
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }
}
