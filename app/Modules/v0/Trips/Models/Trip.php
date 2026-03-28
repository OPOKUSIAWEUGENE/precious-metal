<?php

namespace App\Modules\v0\Trips\Models;

use App\Modules\v0\Fleets\Models\Vehicle;
use App\Modules\v0\Trips\Enums\TripStatus;
use App\Modules\v0\Users\Models\Profile;
use App\Modules\v0\Routes\Models\Route;
use App\Modules\v0\Routes\Models\RouteStop;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Trip extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'trips';

    protected $fillable = [
        'id',
        'route_id',
        'vehicle_id',
        'driver_profile_id',
        'scheduled_at',
        'departure_stop_id',
        'arrival_stop_id',
        'status',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'status' => TripStatus::class,
    ];

    /**
     * Get the route of the trip.
     */
    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    /**
     * Get the vehicle of the trip.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the driver profile of the trip.
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'driver_profile_id');
    }

    /**
     * Get the departure stop of the trip.
     */
    public function departureStop(): BelongsTo
    {
        return $this->belongsTo(RouteStop::class, 'departure_stop_id');
    }

    /**
     * Get the arrival stop of the trip.
     */
    public function arrivalStop(): BelongsTo
    {
        return $this->belongsTo(RouteStop::class, 'arrival_stop_id');
    }

    protected static function boot()
    {
        parent::boot();
    }
}