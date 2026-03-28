<?php

namespace App\Modules\v0\Trips\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RouteStop extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'route_stops';

    protected $fillable = [
        'id',
        'name',
        'latitude',
        'longitude',
        'description',
    ];

    protected static function boot()
    {
        parent::boot();
    }
}