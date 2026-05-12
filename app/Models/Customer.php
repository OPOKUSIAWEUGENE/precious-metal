<?php

namespace App\Models;

use App\Traits\BootTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as AuthenticatableUser;
use Laravel\Sanctum\HasApiTokens;

class Customer extends AuthenticatableUser
{
    use HasApiTokens, HasFactory, BootTrait;

    protected $fillable = [
        'id',
        'email',
        'password',
        'first_name',
        'last_name',
        'date_of_birth',
        'nationality',
        'resident_address',
        'selfie',
        'phone_number',
        'kyc',
        'card_details',
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
            'date_of_birth' => 'date',
            'kyc' => 'array',
            'card_details' => 'array',
        ];
    }
}
