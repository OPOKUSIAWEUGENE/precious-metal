<?php

namespace App\Modules\v0\Fleet\Models;

use App\Modules\v0\Fleet\Enums\VehicleStatus;
use App\Modules\v0\Operator\Models\Operator;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vehicle extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'vehicles';

    protected $fillable = [
        'id',
        'fleet_identifier',
        'operator_id',
        'type',
        'plate_number',
        'capacity',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status' => VehicleStatus::class,
        'capacity' => 'integer',
    ];

    /**
     * Get the operator that owns the vehicle.
     */
    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class);
    }

    protected static function boot()
    {
        parent::boot();
    }
}