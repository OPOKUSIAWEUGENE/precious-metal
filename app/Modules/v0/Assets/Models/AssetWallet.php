<?php

namespace App\Modules\v0\Assets\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class AssetWallet extends Model
{
    use HasFactory;

    protected $table = 'asset_wallets';

    protected $fillable = [
        'owner_type',
        'owner_id',
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

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class, 'asset_wallet_id');
    }
}
