<?php

namespace App\Modules\v0\Callbacks\Models;

use App\Modules\v0\Callbacks\Enums\CallbackStatus;
use App\Modules\v0\Callbacks\Enums\CallbackType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Callback extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'callbacks';

    protected $fillable = [
        'id',
        'type',
        'status',
        'endpoint',
        'method',
        'payload',
        'response',
        'headers',
        'retry_count',
        'scheduled_at',
        'sent_at',
        'delivered_at',
        'error_message',
        'related_type',
        'related_id',
    ];

    protected $casts = [
        'type' => CallbackType::class,
        'status' => CallbackStatus::class,
        'payload' => 'array',
        'response' => 'array',
        'headers' => 'array',
        'retry_count' => 'integer',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    /**
     * Get the related model (polymorphic).
     */
    public function related(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo('related', 'related_type', 'related_id');
    }
}