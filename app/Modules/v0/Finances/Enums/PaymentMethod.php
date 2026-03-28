<?php

namespace App\Modules\v0\Finances\Enums;

enum PaymentMethod: string
{
    case CASH = 'CASH';
    case CARD = 'CARD';
    case MOBILE_MONEY = 'MOBILE_MONEY';
    case BANK_TRANSFER = 'BANK_TRANSFER';
    case WALLET = 'WALLET';
    case CREDIT = 'CREDIT';
}