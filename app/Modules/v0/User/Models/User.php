<?php

namespace App\Modules\v0\User\Models;

use App\Modules\v0\User\Enums\ProfileType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, HasUuids, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
        'first_name',
        'last_name',
        'phone_number',
        'social_login_provider',
        'social_login_id',
        'profile_img',
        'active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'active' => 'boolean',
            'social_login_provider' => 'string',
            'social_login_id' => 'string',
        ];
    }

    /**
     * Get the profiles for the user.
     */
    public function profiles(): HasMany
    {
        return $this->hasMany(Profile::class, 'user_id');
    }

    /**
     * Get the current active profile.
     */
    public function currentProfile(): ?Profile
    {
        return $this->profiles()->where('current', true)->first();
    }

    /**
     * Get the effective profile type based on the current profile.
     */
    public function getEffectiveProfileType(): ProfileType
    {
        $current = $this->currentProfile;
        return $current?->profile_type ?? ProfileType::PASSENGER;
    }

    /**
     * Get the role from the current profile.
     */
    public function getRole(): ?Role
    {
        $current = $this->currentProfile;
        return $current?->role;
    }

    /**
     * Get the primary profile (first profile).
     */
    public function getPrimaryProfile(): ?Profile
    {
        return $this->profiles()->first();
    }

    /**
     * Check if the user is active.
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * Check if the user is a driver.
     */
    public function isDriver(): bool
    {
        return $this->getEffectiveProfileType() === ProfileType::DRIVER;
    }

    /**
     * Check if the user is a platform operator.
     */
    public function isPlatformOperator(): bool
    {
        return $this->getEffectiveProfileType() === ProfileType::PLATFORM_OPERATOR;
    }

    /**
     * Check if the user is a client operator.
     */
    public function isClientOperator(): bool
    {
        return $this->getEffectiveProfileType() === ProfileType::CLIENT_OPERATOR;
    }

    /**
     * Check if the user is an operator (platform or client).
     */
    public function isOperator(): bool
    {
        return in_array($this->getEffectiveProfileType(), [ProfileType::PLATFORM_OPERATOR, ProfileType::CLIENT_OPERATOR]);
    }

    /**
     * Check if the user is a passenger.
     */
    public function isPassenger(): bool
    {
        return $this->getEffectiveProfileType() === ProfileType::PASSENGER;
    }

    /**
     * Get the full name of the user.
     */
    public function getFullNameAttribute(): ?string
    {
        if ($this->first_name && $this->last_name) {
            return "{$this->first_name} {$this->last_name}";
        }
        return $this->first_name ?? $this->email;
    }

    /**
     * Get the authority string for the current role (for authorization).
     */
    public function getAuthorityAttribute(): string
    {
        $role = $this->getRole();
        return $role ? 'ROLE_' . strtoupper($role->name) : '';
    }

    /**
     * Add a profile to the user.
     */
    public function addProfile(Profile $profile): Profile
    {
        $profile->user_id = $this->id;
        return $this->profiles()->save($profile);
    }

    /**
     * Set the current profile.
     */
    public function setCurrentProfile(Profile $profile): bool
    {
        if ($profile->user_id !== $this->id) {
            return false;
        }
        
        $this->profiles()->update(['current' => false]);
        $profile->current = true;
        return $profile->save();
    }

    /**
     * Remove a profile from the user.
     */
    public function removeProfile(Profile $profile): bool
    {
        if ($profile->user_id !== $this->id) {
            return false;
        }
        return $profile->delete();
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope a query to only include users with a specific profile type.
     */
    public function scopeWithProfileType($query, ProfileType $type)
    {
        return $query->whereHas('profiles', function ($q) use ($type) {
            $q->where('profile_type', $type);
        });
    }

    /**
     * Scope a query to only include users with a specific role.
     */
    public function scopeWithRole($query, string $roleName)
    {
        return $query->whereHas('profiles', function ($q) use ($roleName) {
            $q->whereHas('role', function ($r) use ($roleName) {
                $r->where('name', $roleName);
            });
        });
    }

    /**
     * Find user by email.
     */
    public static function findByEmail(string $email): ?self
    {
        return static::where('email', $email)->first();
    }

    /**
     * Find user by social login.
     */
    public static function findBySocialLogin(string $provider, string $socialId): ?self
    {
        return static::where('social_login_provider', $provider)
            ->where('social_login_id', $socialId)
            ->first();
    }

    /**
     * Find user by phone number.
     */
    public static function findByPhone(string $phoneNumber): ?self
    {
        return static::where('phone_number', $phoneNumber)->first();
    }

    /**
     * Create or find user by social login credentials.
     */
    public static function findOrCreateBySocialLogin(array $data): self
    {
        $user = static::findBySocialLogin($data['provider'], $data['social_id']);
        
        if ($user) {
            return $user;
        }

        return static::create([
            'email' => $data['email'],
            'password' => bcrypt(bin2hex(random_bytes(16))),
            'first_name' => $data['first_name'] ?? null,
            'last_name' => $data['last_name'] ?? null,
            'social_login_provider' => $data['provider'],
            'social_login_id' => $data['social_id'],
            'profile_img' => $data['avatar'] ?? null,
            'active' => true,
        ]);
    }

    /**
     * Get all authorities for this user (all roles across all profiles).
     */
    public function getAllAuthorities(): array
    {
        return $this->profiles
            ->where('active', true)
            ->map(function ($profile) {
                $role = $profile->role;
                return $role ? 'ROLE_' . strtoupper($role->name) : null;
            })
            ->filter()
            ->unique()
            ->values()
            ->toArray();
    }

    /**
     * Check if user has a specific authority/role.
     */
    public function hasAuthority(string $authority): bool
    {
        $authorities = $this->getAllAuthorities();
        return in_array($authority, $authorities);
    }

    /**
     * Check if user has any of the given authorities.
     */
    public function hasAnyAuthority(array $authorities): bool
    {
        foreach ($authorities as $authority) {
            if ($this->hasAuthority($authority)) {
                return true;
            }
        }
        return false;
    }
}