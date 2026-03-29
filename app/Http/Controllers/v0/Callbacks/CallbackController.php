<?php

namespace App\Http\Controllers\v0\Callbacks;

use App\Http\Controllers\Controller;
use App\Modules\v0\Callbacks\Services\CallbackService;
use App\Http\Requests\v0\Callbacks\ProcessIncomingRequest;
use App\Http\Requests\v0\Callbacks\SendOutgoingRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CallbackController extends Controller
{
    protected CallbackService $callbackService;

    public function __construct(CallbackService $callbackService)
    {
        $this->callbackService = $callbackService;
    }

    /**
     * Process incoming callback (webhook endpoint).
     */
    public function incoming(Request $request): JsonResponse
    {
        return $this->callbackService->processIncoming($request->all());
    }

    /**
     * Send outgoing callback.
     */
    public function outgoing(SendOutgoingRequest $request): JsonResponse
    {
        return $this->callbackService->sendOutgoing(
            $request->input('endpoint'),
            $request->input('payload'),
            $request->input('related_type'),
            $request->input('related_id')
        );
    }

    /**
     * Get all callbacks.
     */
    public function index(): JsonResponse
    {
        return $this->callbackService->all()->isEmpty()
            ? response()->json(['message' => 'No callbacks found', 'data' => []])
            : response()->json(['data' => $this->callbackService->all()]);
    }

    /**
     * Get callback by ID.
     */
    public function show(string $id): JsonResponse
    {
        $callback = $this->callbackService->find($id);

        if (!$callback) {
            return response()->json(['message' => 'Callback not found'], 404);
        }

        return response()->json(['data' => $callback]);
    }

    /**
     * Get incoming callbacks.
     */
    public function incomingList(): JsonResponse
    {
        return response()->json(['data' => $this->callbackService->getIncoming()]);
    }

    /**
     * Get outgoing callbacks.
     */
    public function outgoingList(): JsonResponse
    {
        return response()->json(['data' => $this->callbackService->getOutgoing()]);
    }

    /**
     * Get callbacks by status.
     */
    public function byStatus(string $status): JsonResponse
    {
        return response()->json(['data' => $this->callbackService->getByStatus($status)]);
    }

    /**
     * Retry a failed callback.
     */
    public function retry(string $id): JsonResponse
    {
        return $this->callbackService->retry($id);
    }

    /**
     * Get callbacks for a related model.
     */
    public function byRelated(Request $request): JsonResponse
    {
        $relatedType = $request->input('related_type');
        $relatedId = $request->input('related_id');

        if (!$relatedType || !$relatedId) {
            return response()->json(['message' => 'related_type and related_id are required'], 400);
        }

        return response()->json(['data' => $this->callbackService->getByRelated($relatedType, $relatedId)]);
    }
}