<?php

namespace App\Modules\v0\Rates\Models;

use App\Traits\BootTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Rate extends Model
{
    use HasFactory, BootTrait;

    protected $table = 'rates';

    protected $fillable = [
        'id',
        'rate',
        'rate_type',
        'status',
        'effective_at',
        'notes',
        'added_by',
    ];

    protected function casts(): array
    {
        return [
            'rate' => 'decimal:4',
            'effective_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $rate): void {
            if (empty($rate->id)) {
                $rate->id = (string) Str::orderedUuid();
            }
        });
    }
}
