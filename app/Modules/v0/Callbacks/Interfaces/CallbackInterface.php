<?php

namespace App\Modules\v0\Callbacks\Interfaces;

use App\Modules\v0\Callbacks\Models\Callback;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

interface CallbackInterface
{
    /**
     * Process an incoming callback (webhook).
     */
    public function processIncoming(array $data): JsonResponse;

    /**
     * Create and send an outgoing callback.
     */
    public function sendOutgoing(string $endpoint, array $payload, ?string $relatedType = null, ?string $relatedId = null): JsonResponse;

    /**
     * Get all callbacks.
     */
    public function all(): Collection;

    /**
     * Find callback by ID.
     */
    public function find(string $id): ?Callback;

    /**
     * Get incoming callbacks.
     */
    public function getIncoming(): Collection;

    /**
     * Get outgoing callbacks.
     */
    public function getOutgoing(): Collection;

    /**
     * Get callbacks by status.
     */
    public function getByStatus(string $status): Collection;

    /**
     * Retry a failed callback.
     */
    public function retry(string $id): JsonResponse;

    /**
     * Get callbacks for a related model.
     */
    public function getByRelated(string $relatedType, string $relatedId): Collection;
}