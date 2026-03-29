<?php

namespace App\Modules\v0\User\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'roles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get the profiles for the role.
     */
    public function profiles(): HasMany
    {
        return $this->hasMany(Profile::class, 'role_id');
    }

    /**
     * Check if this is a default role.
     */
    public function isDefault(): bool
    {
        return in_array(strtolower($this->name), ['user', 'passenger', 'user_role']);
    }

    /**
     * Find a role by name.
     */
    public static function findByName(string $name): ?self
    {
        return static::where('name', $name)->first();
    }
}