<?php

namespace App\Modules\v0\Callbacks\Enums;

enum CallbackStatus: string
{
    case PENDING = 'PENDING';
    case SENT = 'SENT';
    case FAILED = 'FAILED';
    case DELIVERED = 'DELIVERED';
}