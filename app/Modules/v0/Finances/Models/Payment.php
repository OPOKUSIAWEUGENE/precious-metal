<?php

namespace App\Modules\v0\Finances\Models;

use App\Modules\v0\Finances\Enums\PaymentMethod;
use App\Modules\v0\Finances\Enums\PaymentStatus;
use App\Modules\v0\Trips\Models\Booking;
use App\Modules\v0\Users\Models\Profile;
use App\Modules\v0\Operators\Models\Operator;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'payments';

    protected $fillable = [
        'id',
        'booking_id',
        'passenger_profile_id',
        'operator_id',
        'amount',
        'currency',
        'payment_method',
        'status',
        'transaction_id',
        'external_reference',
        'gateway',
        'gateway_response',
        'metadata',
        'paid_at',
        'failed_at',
        'refunded_at',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_method' => PaymentMethod::class,
        'status' => PaymentStatus::class,
        'gateway_response' => 'array',
        'metadata' => 'array',
        'paid_at' => 'datetime',
        'failed_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    /**
     * Get the booking associated with the payment.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the passenger profile associated with the payment.
     */
    public function passengerProfile(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'passenger_profile_id');
    }

    /**
     * Get the operator associated with the payment.
     */
    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class);
    }
}