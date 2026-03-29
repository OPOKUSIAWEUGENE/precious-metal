<?php

namespace App\Modules\v0\User\Models;

use App\Modules\v0\User\Enums\DriverStatus;
use App\Modules\v0\User\Enums\ProfileType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Profile extends Model
{
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'profiles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
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

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'profile_type' => ProfileType::class,
            'driver_status' => DriverStatus::class,
            'active' => 'boolean',
            'current' => 'boolean',
            'created_by' => 'uuid',
            'updated_by' => 'uuid',
        ];
    }

    /**
     * Get the user that owns the profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the role associated with the profile.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Get the operator associated with the profile (for CLIENT_OPERATOR profile type).
     */
    public function operator(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\v0\Operator\Models\Operator::class, 'operator_id');
    }

    /**
     * Check if this is the current profile.
     */
    public function isCurrent(): bool
    {
        return $this->current;
    }

    /**
     * Check if this profile is active.
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * Check if this is a driver profile.
     */
    public function isDriver(): bool
    {
        return $this->profile_type === ProfileType::DRIVER;
    }

    /**
     * Check if this is an operator profile.
     */
    public function isOperator(): bool
    {
        return in_array($this->profile_type, [ProfileType::PLATFORM_OPERATOR, ProfileType::CLIENT_OPERATOR]);
    }

    /**
     * Get the role name.
     */
    public function getRoleName(): ?string
    {
        return $this->role?->name;
    }

    /**
     * Scope a query to only include active profiles.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope a query to only include current profiles.
     */
    public function scopeCurrent($query)
    {
        return $query->where('current', true);
    }

    /**
     * Scope a query to only include profiles of a specific type.
     */
    public function scopeOfType($query, ProfileType $type)
    {
        return $query->where('profile_type', $type);
    }
}