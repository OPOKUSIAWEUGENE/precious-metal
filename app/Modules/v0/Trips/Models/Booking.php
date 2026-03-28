<?php

namespace App\Modules\v0\Trips\Models;

use App\Modules\v0\Trips\Enums\BookingStatus;
use App\Modules\v0\Users\Models\Profile;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'bookings';

    protected $fillable = [
        'id',
        'trip_id',
        'passenger_profile_id',
        'seat_count',
        'status',
    ];

    protected $casts = [
        'seat_count' => 'integer',
        'status' => BookingStatus::class,
    ];

    /**
     * Get the trip of the booking.
     */
    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    /**
     * Get the passenger profile of the booking.
     */
    public function passenger(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'passenger_profile_id');
    }

    protected static function boot()
    {
        parent::boot();
    }
}