<?php

namespace App\Modules\v0\Trips\Enums;

enum RouteStatus: string
{
    case ACTIVE = 'ACTIVE';
    case INACTIVE = 'INACTIVE';
    case ARCHIVED = 'ARCHIVED';
}