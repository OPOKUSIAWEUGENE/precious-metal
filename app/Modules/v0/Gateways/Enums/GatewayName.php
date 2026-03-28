<?php

namespace App\Modules\v0\Gateways\Enums;

enum GatewayName: string
{
    case HUBTEL = 'HUBTEL';
    case STRIPE = 'STRIPE';
    case PAYSTACK = 'PAYSTACK';
}