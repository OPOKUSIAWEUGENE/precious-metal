<?php

namespace App\Http\Controllers\v0\Assets;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AssetWallet',
    title: 'Asset Wallet',
    description: 'A polymorphic asset wallet belonging to a customer or platform user.',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'owner_type', type: 'string', example: 'App\\\\Modules\\\\v0\\\\Customers\\\\Models\\\\Customer'),
        new OA\Property(property: 'owner_id', type: 'string', format: 'uuid'),
        new OA\Property(property: 'user_id', type: 'string', format: 'uuid', nullable: true),
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
#[OA\Schema(
    schema: 'Withdrawal',
    title: 'Withdrawal',
    description: 'An asset withdrawal request.',
    properties: [
        new OA\Property(property: 'id', type: 'string', format: 'uuid'),
        new OA\Property(
            property: 'asset_wallet_id',
            description: 'The internal asset wallet primary key.',
            type: 'integer'
        ),
        new OA\Property(property: 'owner_type', type: 'string'),
        new OA\Property(property: 'owner_id', type: 'string', format: 'uuid'),
        new OA\Property(property: 'rate_id', type: 'string', format: 'uuid', nullable: true),
        new OA\Property(property: 'requested_amount', type: 'string', example: '2.00000000'),
        new OA\Property(property: 'asset_amount', type: 'string', example: '2.00000000'),
        new OA\Property(property: 'cash_amount', type: 'string', example: '2500.00'),
        new OA\Property(property: 'conversion_rate', type: 'string', example: '1250.0000'),
        new OA\Property(property: 'conversion_type', type: 'string', example: 'gold_to_cash'),
        new OA\Property(property: 'status', type: 'string', example: 'pending'),
        new OA\Property(property: 'notes', type: 'string', nullable: true),
        new OA\Property(property: 'approved_by', type: 'string', format: 'uuid', nullable: true),
        new OA\Property(property: 'approved_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'rejected_by', type: 'string', format: 'uuid', nullable: true),
        new OA\Property(property: 'rejected_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'rejection_reason', type: 'string', nullable: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', nullable: true),
    ]
)]
#[OA\Schema(
    schema: 'CustomerRate',
    title: 'Customer Rate',
    description: 'A customer-facing active rate entry.',
    properties: [
        new OA\Property(property: 'id', type: 'string', format: 'uuid'),
        new OA\Property(property: 'rate_type', type: 'string', example: 'buying', enum: ['buying', 'selling']),
        new OA\Property(property: 'rate', type: 'string', example: '1250.0000'),
        new OA\Property(property: 'status', type: 'string', example: 'active'),
        new OA\Property(property: 'effective_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'notes', type: 'string', nullable: true),
    ]
)]
#[OA\Schema(
    schema: 'WalletQuote',
    title: 'Wallet Quote',
    description: 'A quotation for buying, selling, or transferring gold.',
    properties: [
        new OA\Property(property: 'id', type: 'string', format: 'uuid'),
        new OA\Property(property: 'reference', type: 'string', example: 'BUY-20260730-ABC123'),
        new OA\Property(property: 'operation', type: 'string', example: 'buy'),
        new OA\Property(property: 'status', type: 'string', example: 'active'),
        new OA\Property(property: 'currency', type: 'string', example: 'GHS'),
        new OA\Property(property: 'cash_amount', type: 'string', nullable: true),
        new OA\Property(property: 'gold_weight', type: 'string', nullable: true),
        new OA\Property(property: 'price_per_gram', type: 'string', nullable: true),
        new OA\Property(property: 'fee_rate', type: 'string', nullable: true),
        new OA\Property(property: 'fee_amount', type: 'string', nullable: true),
        new OA\Property(property: 'total_payable', type: 'string', nullable: true),
        new OA\Property(property: 'total_receivable', type: 'string', nullable: true),
        new OA\Property(property: 'recipient_type', type: 'string', nullable: true),
        new OA\Property(property: 'recipient_id', type: 'string', format: 'uuid', nullable: true),
        new OA\Property(property: 'expires_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'metadata', type: 'object', nullable: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', nullable: true),
    ]
)]
#[OA\Schema(
    schema: 'WalletTransaction',
    title: 'Wallet Transaction',
    description: 'A wallet transaction record.',
    properties: [
        new OA\Property(property: 'id', type: 'string', format: 'uuid'),
        new OA\Property(property: 'reference', type: 'string', example: 'BUY-20260730-ABC123'),
        new OA\Property(property: 'group_reference', type: 'string', nullable: true),
        new OA\Property(property: 'operation', type: 'string', example: 'buy'),
        new OA\Property(property: 'direction', type: 'string', example: 'credit'),
        new OA\Property(property: 'status', type: 'string', example: 'completed'),
        new OA\Property(property: 'wallet_id', type: 'integer'),
        new OA\Property(property: 'owner_type', type: 'string'),
        new OA\Property(property: 'owner_id', type: 'string', format: 'uuid'),
        new OA\Property(property: 'counterparty_type', type: 'string', nullable: true),
        new OA\Property(property: 'counterparty_id', type: 'string', format: 'uuid', nullable: true),
        new OA\Property(property: 'quote_reference', type: 'string', nullable: true),
        new OA\Property(property: 'asset_amount', type: 'string', example: '1.50000000'),
        new OA\Property(property: 'cash_amount', type: 'string', nullable: true),
        new OA\Property(property: 'price_per_gram', type: 'string', nullable: true),
        new OA\Property(property: 'fee_amount', type: 'string', nullable: true),
        new OA\Property(property: 'total_amount', type: 'string', nullable: true),
        new OA\Property(property: 'narration', type: 'string', nullable: true),
        new OA\Property(property: 'metadata', type: 'object', nullable: true),
        new OA\Property(property: 'completed_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', nullable: true),
    ]
)]
class AssetSchemas
{
}
