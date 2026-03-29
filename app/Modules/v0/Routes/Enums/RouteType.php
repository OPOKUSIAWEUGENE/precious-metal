<?php

namespace App\Modules\v0\Routes\Enums;

enum RouteType: string
{
    case INTERCITY = 'INTERCITY';
    case INTRACITY = 'INTRACITY';
    case EXPRESS = 'EXPRESS';
}