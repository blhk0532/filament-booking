<?php

namespace App;

enum UserRole: string
{
    case ADMIN = 'admin';
    case SERVICE = 'service';
    case BOOKING = 'booking';
    case USER = 'user';

    public function label(): string
    {
        return match ($this) {
            UserRole::ADMIN => 'Administrator',
            UserRole::SERVICE => 'Doctor',
            UserRole::BOOKING => 'Booking',
            UserRole::USER => 'User',
        };
    }
}
