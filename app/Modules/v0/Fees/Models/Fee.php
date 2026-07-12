<?php

namespace App\Modules\v0\Fees\Models;

use App\Traits\BootTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Fee extends Model
{
    use HasFactory, BootTrait;

    protected $table = 'fees';

    protected $fillable = [
        'id',
        'fee',
        'status',
        'effective_at',
        'notes',
        'added_by',
    ];

    protected function casts(): array
    {
        return [
            'fee' => 'decimal:4',
            'effective_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $fee): void {
            if (empty($fee->id)) {
                $fee->id = (string) Str::orderedUuid();
            }
        });
    }
}
