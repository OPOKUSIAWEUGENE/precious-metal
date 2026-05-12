<?php
 
namespace App\Models;
 
use App\Traits\BootTrait;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;
 
class PersonalAccessToken extends SanctumPersonalAccessToken
{
    use BootTrait;

    protected $fillable = [
        'id',
        'name',
        'token',
        'abilities',
        'expires_at',
    ];
}
