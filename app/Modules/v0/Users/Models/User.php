<?php

namespace App\Modules\v0\Users\Models;

use App\Modules\v0\GoldWallets\Models\GoldWallet;
use App\Traits\BootTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, BootTrait;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'email',
        'password',
        'change_password',
        'first_name',
        'last_name',
        'phone_number',
        'profile_img',
        'role',
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
            'change_password' => 'boolean',
            'active' => 'boolean',
        ];
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) \Illuminate\Support\Str::orderedUuid();
            }
        });

        static::created(function (self $user): void {
            $user->goldWallets()->firstOrCreate(
                [
                    'weight_unit' => 'gram',
                    'purity' => '0.999900',
                ],
                [
                    'available_weight' => 0,
                    'reserved_weight' => 0,
                    'status' => 'active',
                ]
            );
        });
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
        return $this->role === $role;
    }

    /**
     * Get the list of roles (returns array for compatibility).
     */
    public function getRoleNames(): array
    {
        return $this->role ? [$this->role] : [];
    }

    public function isPlatformAdmin(): bool
    {
        return in_array($this->role, ['admin', 'platform_admin'], true);
    }

    public function goldWallets(): HasMany
    {
        return $this->hasMany(GoldWallet::class, 'user_id');
    }
}
