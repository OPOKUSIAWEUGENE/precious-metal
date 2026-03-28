<?php

namespace App\Modules\v0\Finances\Models;

use App\Modules\v0\Finances\Enums\SettlementStatus;
use App\Modules\v0\Users\Models\Profile;
use App\Modules\v0\Operators\Models\Operator;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Settlement extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'settlements';

    protected $fillable = [
        'id',
        'operator_id',
        'driver_profile_id',
        'period_start',
        'period_end',
        'total_amount',
        'commission_amount',
        'net_amount',
        'status',
        'payment_method',
        'payment_reference',
        'processed_at',
        'notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'status' => SettlementStatus::class,
        'period_start' => 'date',
        'period_end' => 'date',
        'processed_at' => 'datetime',
    ];

    /**
     * Get the operator associated with the settlement.
     */
    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class);
    }

    /**
     * Get the driver profile associated with the settlement.
     */
    public function driverProfile(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'driver_profile_id');
    }

    /**
     * Get the commissions associated with the settlement.
     */
    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class);
    }
}