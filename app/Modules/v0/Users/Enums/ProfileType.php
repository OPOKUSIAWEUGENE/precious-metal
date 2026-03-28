<?php

namespace App\Modules\v0\Users\Enums;

enum ProfileType: string
{
    case PLATFORM_OPERATOR = 'PLATFORM_OPERATOR';
    case CLIENT_OPERATOR = 'CLIENT_OPERATOR';
    case PASSENGER = 'PASSENGER';
    case DRIVER = 'DRIVER';
}