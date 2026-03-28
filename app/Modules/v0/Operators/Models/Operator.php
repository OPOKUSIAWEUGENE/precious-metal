<?php

namespace App\Modules\v0\Operators\Models;

use App\Modules\v0\Operator\Enums\OperatorType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Operator extends Model
{
    use HasFactory;

    protected $table = 'operators';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'name_alias',
        'operator_type',
        'contact_email',
        'contact_phone',
        'logo_url',
        'profile',
        'active',
    ];

    protected $casts = [
        'operator_type' => OperatorType::class,
        'active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }
}