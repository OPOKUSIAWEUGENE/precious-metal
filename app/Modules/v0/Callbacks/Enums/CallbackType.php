<?php

namespace App\Modules\v0\Callbacks\Enums;

enum CallbackType: string
{
    case INCOMING = 'INCOMING';
    case OUTGOING = 'OUTGOING';
}