<?php

namespace App\Modules\v0\Routes\Enums;

enum RouteType: string
{
    case INTERCITY = 'INTERCITY';
    case INTRA_CITY = 'INTRA_CITY';
    case EXPRESS = 'EXPRESS';
}