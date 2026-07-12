<?php

namespace App\Http\Controllers\v0\GoldWallets;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'GoldWallet',
    title: 'Gold Wallet',
    description: 'A gold wallet belonging to a user.',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'user_id', type: 'string', format: 'uuid'),
        new OA\Property(property: 'wallet_reference', type: 'string', format: 'uuid'),
        new OA\Property(property: 'available_weight', type: 'string', example: '0.00000000'),
        new OA\Property(property: 'reserved_weight', type: 'string', example: '0.00000000'),
        new OA\Property(property: 'weight_unit', type: 'string', example: 'gram'),
        new OA\Property(property: 'purity', type: 'string', example: '0.999900'),
        new OA\Property(property: 'status', type: 'string', example: 'active'),
        new OA\Property(property: 'last_transaction_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', nullable: true),
    ]
)]
class GoldWalletSchema
{
}
