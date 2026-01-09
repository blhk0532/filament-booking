<?php

namespace Adultdate\FilamentBooking\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use WallaceMartinss\FilamentEvolution\Models\WhatsappInstance;

class BookingCalendar extends Model
{
    protected $fillable = [
        'name',
        'google_calendar_id',
        'whatsapp_id',
        'creator_id',
        'owner_id',
        'notification_user_ids',
        'access',
        'is_active',
    ];

    protected $casts = [
        'access' => 'array',
        'notification_user_ids' => 'array',
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

    public function whatsappInstance(): BelongsTo
    {
        return $this->belongsTo(WhatsappInstance::class, 'whatsapp_id');
    }
}
