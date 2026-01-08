<?php

namespace Adultdate\FilamentBooking\Models\Booking;

use Adultdate\FilamentBooking\Models\Address;
use Adultdate\FilamentBooking\Models\BookingComment;
use Database\Factories\Booking\ClientFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Client extends Model
{
    /** @use HasFactory<\Adultdate\FilamentBooking\Database\Factories\Booking\ClientFactory> */
    use HasFactory;

    use SoftDeletes;

    protected static function newFactory()
    {
        return \Adultdate\FilamentBooking\Database\Factories\Booking\ClientFactory::new();
    }

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Client $client) {
            if (empty($client->ulid)) {
                $client->ulid = (string) Str::ulid();
            }
        });
    }

    /**
     * @var string
     */
    protected $table = 'booking_clients';

    /**
     * @var string
     */
    protected $keyType = 'int';

    /**
     * @var bool
     */
    public $incrementing = true;

    /**
     * @var array<string>
     */
    protected $fillable = [
        'ulid',
        'name',
        'address',
        'street',
        'city',
        'zip',
        'phone',
        'email',
        'phones',
        'dob',
        'birthday',
        'photo',
        'notes',
        'type',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'name' => 'string',
        'birthday' => 'date',
        'address' => 'string',
        'street' => 'string',
        'city' => 'string',
        'zip' => 'string',
        'phone' => 'string',
        'email' => 'string',
        'phones' => 'array',
        'dob' => 'string',
        'photo' => 'string',
        'notes' => 'string',
        'type' => 'string',
    ];

    /** @return MorphToMany<Address, $this> */
    public function addresses(): MorphToMany
    {
        return $this->morphToMany(Address::class, 'booking_addressable');
    }

    /** @return HasMany<Comment, $this> */
    public function comments(): HasMany
    {
        return $this->hasMany(BookingComment::class);
    }

    /** @return HasManyThrough<Payment, Order, $this> */
    public function payments(): HasManyThrough
    {
        return $this->hasManyThrough(Payment::class, Order::class, 'booking_client_id');
    }
}