<?php

namespace App\Modules\v0\Assets\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WalletQuote extends Model
{
    use HasFactory;

    protected $table = 'wallet_quotes';

    protected $fillable = [
        'id',
        'reference',
        'operation',
        'status',
        'currency',
        'cash_amount',
        'gold_weight',
        'price_per_gram',
        'fee_rate',
        'fee_amount',
        'total_payable',
        'total_receivable',
        'recipient_type',
        'recipient_id',
        'source_wallet_id',
        'destination_wallet_id',
        'expires_at',
        'metadata',
        'created_by_type',
        'created_by_id',
    ];

    protected function casts(): array
    {
        return [
            'cash_amount' => 'decimal:2',
            'gold_weight' => 'decimal:8',
            'price_per_gram' => 'decimal:4',
            'fee_rate' => 'decimal:4',
            'fee_amount' => 'decimal:2',
            'total_payable' => 'decimal:2',
            'total_receivable' => 'decimal:2',
            'expires_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $quote): void {
            if (empty($quote->id)) {
                $quote->id = (string) Str::orderedUuid();
            }

            if (empty($quote->reference)) {
                $quote->reference = strtoupper($quote->operation) . '-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
            }
        });
    }
}
