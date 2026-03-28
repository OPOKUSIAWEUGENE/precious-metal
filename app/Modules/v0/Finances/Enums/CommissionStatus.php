<?php

namespace App\Modules\v0\Finances\Enums;

enum CommissionStatus: string
{
    case PENDING = 'PENDING';
    case CALCULATED = 'CALCULATED';
    case APPROVED = 'APPROVED';
    case PAID = 'PAID';
    case FAILED = 'FAILED';
}