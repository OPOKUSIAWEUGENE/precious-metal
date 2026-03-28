<?php

namespace App\Modules\v0\Finances\Models;

use App\Modules\v0\Finances\Enums\CommissionStatus;
use App\Modules\v0\Users\Models\Profile;
use App\Modules\v0\Operators\Models\Operator;
use App\Modules\v0\Trips\Models\Trip;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Commission extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'commissions';

    protected $fillable = [
        'id',
        'trip_id',
        'operator_id',
        'driver_profile_id',
        'settlement_id',
        'amount',
        'rate',
        'gross_amount',
        'net_amount',
        'status',
        'period_start',
        'period_end',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'rate' => 'decimal:4',
        'gross_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'status' => CommissionStatus::class,
        'period_start' => 'date',
        'period_end' => 'date',
    ];

    /**
     * Get the trip associated with the commission.
     */
    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    /**
     * Get the operator associated with the commission.
     */
    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class);
    }

    /**
     * Get the driver profile associated with the commission.
     */
    public function driverProfile(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'driver_profile_id');
    }

    /**
     * Get the settlement associated with the commission.
     */
    public function settlement(): BelongsTo
    {
        return $this->belongsTo(Settlement::class);
    }
}