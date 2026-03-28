<?php

namespace App\Modules\v0\Trips\Enums;

enum BookingStatus: string
{
    case CONFIRMED = 'CONFIRMED';
    case CANCELLED = 'CANCELLED';
    case COMPLETED = 'COMPLETED';
}