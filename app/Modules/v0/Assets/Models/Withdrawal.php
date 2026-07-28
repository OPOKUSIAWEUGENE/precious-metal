<?php

namespace App\Modules\v0\Assets\Models;

use App\Modules\v0\Rates\Models\Rate;
use App\Modules\v0\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class Withdrawal extends Model
{
    use HasFactory;

    protected $table = 'withdrawals';

    protected $fillable = [
        'id',
        'asset_wallet_id',
        'owner_type',
        'owner_id',
        'rate_id',
        'requested_amount',
        'asset_amount',
        'cash_amount',
        'conversion_rate',
        'conversion_type',
        'status',
        'notes',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'requested_amount' => 'decimal:8',
            'asset_amount' => 'decimal:8',
            'cash_amount' => 'decimal:2',
            'conversion_rate' => 'decimal:4',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $withdrawal): void {
            if (empty($withdrawal->id)) {
                $withdrawal->id = (string) Str::orderedUuid();
            }
        });
    }

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(AssetWallet::class, 'asset_wallet_id');
    }

    public function rate(): BelongsTo
    {
        return $this->belongsTo(Rate::class, 'rate_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
