<?php

namespace App\Modules\v0\GoldWallets\Models;

use App\Modules\v0\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class GoldWallet extends Model
{
    use HasFactory;

    protected $table = 'gold_wallets';

    protected $fillable = [
        'user_id',
        'wallet_reference',
        'available_weight',
        'reserved_weight',
        'weight_unit',
        'purity',
        'status',
        'last_transaction_at',
    ];

    protected function casts(): array
    {
        return [
            'available_weight' => 'decimal:8',
            'reserved_weight' => 'decimal:8',
            'purity' => 'decimal:6',
            'last_transaction_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $wallet): void {
            if (empty($wallet->wallet_reference)) {
                $wallet->wallet_reference = (string) Str::uuid();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
