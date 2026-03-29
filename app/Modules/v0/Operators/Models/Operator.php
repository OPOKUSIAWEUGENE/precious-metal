<?php

namespace App\Modules\v0\Operators\Models;

use App\Modules\v0\Operators\Enums\OperatorType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Operator extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'operators';

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

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Str::orderedUuid()->toString();
            }
        });
    }

    /**
     * Tell database not to auto increment this column
     */
    public function getIncrementing()
    {
        return false;
    }

    /**
     * get the auto-incrementing key type
     */
    public function getKeyType()
    {
        return "string";
    }
}