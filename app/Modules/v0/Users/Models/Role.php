<?php

namespace App\Modules\v0\Users\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'roles';

    protected $fillable = [
        'id',
        'name',
        'description',
    ];

    protected static function boot()
    {
        parent::boot();
    }
}