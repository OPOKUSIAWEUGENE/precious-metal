<?php

namespace App\Modules\v0\Trips\Models;

use App\Modules\v0\Trips\Enums\RouteStatus;
use App\Modules\v0\Trips\Enums\RouteType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Route extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'routes';

    protected $fillable = [
        'id',
        'name',
        'start_stop_id',
        'end_stop_id',
        'status',
        'route_type',
    ];

    protected $casts = [
        'status' => RouteStatus::class,
        'route_type' => RouteType::class,
    ];

    /**
     * Get the start stop of the route.
     */
    public function startStop(): BelongsTo
    {
        return $this->belongsTo(RouteStop::class, 'start_stop_id');
    }

    /**
     * Get the end stop of the route.
     */
    public function endStop(): BelongsTo
    {
        return $this->belongsTo(RouteStop::class, 'end_stop_id');
    }

    protected static function boot()
    {
        parent::boot();
    }
}