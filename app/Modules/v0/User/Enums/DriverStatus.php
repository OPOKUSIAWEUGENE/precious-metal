<?php

namespace App\Modules\v0\User\Enums;

enum DriverStatus: string
{
    case AVAILABLE = 'AVAILABLE';
    case ON_TRIP = 'ON_TRIP';
    case OFFLINE = 'OFFLINE';
    case BUSY = 'BUSY';
}