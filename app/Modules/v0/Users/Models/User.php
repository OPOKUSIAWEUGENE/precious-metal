<?php

namespace App\Modules\v0\Users\Models;

use App\Traits\BootTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, BootTrait;

    protected $fillable = [
        'id',
        'email',
        'password',
        'first_name',
        'last_name',
        'phone_number',
        'profile_img',
        'roles',
        'active',
    ];

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
            'password' => 'hashed',
            'active' => 'boolean',
            'roles' => 'array',
        ];
    }

    /**
     * Get the username (email) for authentication.
     */
    protected function username(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->email,
        );
    }

    /**
     * Check if the user has a specific role.
     */
    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles ?? []);
    }

    /**
     * Get the list of roles.
     */
    public function getRoleNames(): array
    {
        return $this->roles ?? [];
    }
}
