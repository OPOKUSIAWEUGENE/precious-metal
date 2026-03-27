<?php

namespace App\Modules\v0\Fleet\Enums;

enum VehicleStatus: string
{
    case ACTIVE = 'ACTIVE';
    case INACTIVE = 'INACTIVE';
    case MAINTENANCE = 'MAINTENANCE';
    case RETIRED = 'RETIRED';
}