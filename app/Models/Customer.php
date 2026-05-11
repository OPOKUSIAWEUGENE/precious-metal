<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as AuthenticatableUser;
use Laravel\Sanctum\HasApiTokens;

class Customer extends AuthenticatableUser
{
    use HasApiTokens, HasFactory, HasUuids;

    protected $table = 'customers';
    public $incrementing = false;
    protected $keyType = 'string';

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

    protected $casts = [
        'password' => 'hashed',
        'active' => 'boolean',
        'date_of_birth' => 'date',
        'kyc' => 'array',
        'card_details' => 'array',
    ];
}
