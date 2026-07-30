<?php

namespace App\Modules\v0\Assets\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $table = 'wallet_transactions';

    protected $fillable = [
        'id',
        'reference',
        'group_reference',
        'operation',
        'direction',
        'status',
        'wallet_id',
        'owner_type',
        'owner_id',
        'counterparty_type',
        'counterparty_id',
        'quote_reference',
        'asset_amount',
        'cash_amount',
        'price_per_gram',
        'fee_amount',
        'total_amount',
        'narration',
        'metadata',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'asset_amount' => 'decimal:8',
            'cash_amount' => 'decimal:2',
            'price_per_gram' => 'decimal:4',
            'fee_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'metadata' => 'array',
            'completed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $transaction): void {
            if (empty($transaction->id)) {
                $transaction->id = (string) Str::orderedUuid();
            }

            if (empty($transaction->reference)) {
                $transaction->reference = strtoupper($transaction->operation) . '-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
            }
        });
    }
}
