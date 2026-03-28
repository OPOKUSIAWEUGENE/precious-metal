<?php

namespace App\Modules\v0\Users\Models;

use App\Modules\v0\Users\Enums\ProfileType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Collection;

class User extends Authenticatable
{
    use HasFactory, HasUuids, HasApiTokens;

    protected $table = 'users';

    protected $fillable = [
        'id',
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

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'active' => 'boolean',
    ];

    /**
     * Get the profiles associated with the user.
     */
    public function profiles(): HasMany
    {
        return $this->hasMany(Profile::class);
    }

    /**
     * Get the current active profile.
     */
    public function getCurrentProfile(): ?Profile
    {
        return $this->profiles->filter(fn($profile) => $profile->current)->first() 
            ?? $this->profiles->first();
    }

    /**
     * Get the effective profile type.
     */
    public function getEffectiveProfileType(): ProfileType
    {
        $current = $this->getCurrentProfile();
        return ($current && $current->profileType) ? $current->profileType : ProfileType::PASSENGER;
    }

    /**
     * Get the role from current profile.
     */
    public function getRole(): ?Role
    {
        $current = $this->getCurrentProfile();
        return $current?->role;
    }

    /**
     * Add a profile to the user.
     */
    public function addProfile(Profile $profile): void
    {
        $this->profiles()->save($profile);
    }

    /**
     * Clear all profiles.
     */
    public function clearProfiles(): void
    {
        $this->profiles()->delete();
    }

    /**
     * Get the role name for authorization.
     */
    public function getRoleName(): ?string
    {
        $role = $this->getRole();
        return $role?->name;
    }

    /**
     * Get authorities for authentication.
     */
    public function getAuthorities(): Collection
    {
        $roleName = $this->getRoleName();
        if ($roleName) {
            return collect(['ROLE_' . strtoupper($roleName)]);
        }
        return collect();
    }

    /**
     * Get the username (email) for authentication.
     */
    public function getUsername(): string
    {
        return $this->email;
    }

    protected static function boot()
    {
        parent::boot();
    }
}