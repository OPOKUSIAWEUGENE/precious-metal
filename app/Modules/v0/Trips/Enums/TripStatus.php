<?php

namespace App\Modules\v0\Trips\Enums;

enum TripStatus: string
{
    case PENDING_PAYMENT = 'PENDING_PAYMENT';
    case SCHEDULED = 'SCHEDULED';
    case IN_PROGRESS = 'IN_PROGRESS';
    case COMPLETED = 'COMPLETED';
    case CANCELLED = 'CANCELLED';
}