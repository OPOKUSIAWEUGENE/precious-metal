<?php

namespace App\Modules\v0\Fleet\Models;

use App\Modules\v0\User\Models\Profile;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DriverVehicle extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'driver_vehicles';

    protected $fillable = [
        'id',
        'profile_id',
        'vehicle_id',
    ];

    /**
     * Get the profile (driver) associated with this assignment.
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    /**
     * Get the vehicle associated with this assignment.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    protected static function boot()
    {
        parent::boot();
    }
}