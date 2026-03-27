<?php

namespace App\Modules\v0\User\Models;

use App\Modules\v0\User\Enums\DriverStatus;
use App\Modules\v0\User\Enums\ProfileType;
use App\Modules\v0\Operator\Models\Operator;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'profiles';

    protected $fillable = [
        'id',
        'profile_type',
        'role_id',
        'operator_id',
        'user_id',
        'active',
        'current',
        'license',
        'driver_status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'profile_type' => ProfileType::class,
        'driver_status' => DriverStatus::class,
        'active' => 'boolean',
        'current' => 'boolean',
    ];

    /**
     * Get the user that owns the profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the role associated with the profile.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the operator associated with the profile.
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