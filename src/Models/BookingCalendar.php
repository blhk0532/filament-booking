<?php

namespace Adultdate\FilamentBooking\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingCalendar extends Model
{
    protected $fillable = [
        'name',
        'creator_id',
        'owner_id',
        'access',
        'is_active',
    ];

    protected $casts = [
        'access' => 'array',
        'is_active' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
