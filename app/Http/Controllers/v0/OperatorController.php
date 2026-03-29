<?php

namespace App\Http\Controllers\v0;

use App\Http\Controllers\Controller;
use App\Http\Requests\v0\Operator\StoreOperatorRequest;
use App\Http\Requests\v0\Operator\UpdateOperatorRequest;
use App\Modules\v0\Operator\Services\OperatorService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(schema="StoreOperatorRequest",
 *     required={"name", "code", "type", "email", "phone"},
 *     @OA\Property(property="name", type="string", example="Accra Metro Transport"),
 *     @OA\Property(property="code", type="string", example="AMT001"),
 *     @OA\Property(property="type", type="string", enum={"PLATFORM", "CLIENT"}),
 *     @OA\Property(property="email", type="string", example="info@accrametro.com"),
 *     @OA\Property(property="phone", type="string", example="+233300000001"),
 *     @OA\Property(property="address", type="string", example="Accra Central, Ghana")
 * )
 * 
 * @OA\Schema(schema="UpdateOperatorRequest",
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="type", type="string", enum={"PLATFORM", "CLIENT"}),
 *     @OA\Property(property="email", type="string"),
 *     @OA\Property(property="phone", type="string"),
 *     @OA\Property(property="address", type="string"),
 *     @OA\Property(property="active", type="boolean")
 * )
 */
class OperatorController extends Controller
{
    protected OperatorService $service;

    public function __construct(OperatorService $service)
    {
        $this->service = $service;
    }

    /**
     * List all operators.
     *
     * @OA\Get(
     *     path="/api/v0/operators",
     *     tags={"Operators - Platform Operator"},
     *     summary="List all operators",
     *     description="Get a list of all operators in the system. Platform Operator only.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Operator")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden - Requires Platform Operator role")
     * )
     */
    public function index(): JsonResponse
    {
        return $this->service->all();
    }

    /**
     * Create a new operator.
     *
     * @OA\Post(
     *     path="/api/v0/operators",
     *     tags={"Operators - Platform Operator"},
     *     summary="Create operator",
     *     description="Create a new client operator. Platform Operator only.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreOperatorRequest")
     *     ),
     *     @OA\Response(response=201, description="Operator created", @OA\JsonContent(ref="#/components/schemas/Operator")),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(StoreOperatorRequest $request): JsonResponse
    {
        return $this->service->create($request->validated());
    }

    /**
     * Get operator details.
     *
     * @OA\Get(
     *     path="/api/v0/operators/{id}",
     *     tags={"Operators - Platform Operator"},
     *     summary="Get operator",
     *     description="Get details of a specific operator by ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\Response(response=200, description="Successful response", @OA\JsonContent(ref="#/components/schemas/Operator")),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Operator not found")
     * )
     */
    public function show(string $id): JsonResponse
    {
        return $this->service->find($id);
    }

    /**
     * Update an operator.
     *
     * @OA\Put(
     *     path="/api/v0/operators/{id}",
     *     tags={"Operators - Platform Operator"},
     *     summary="Update operator",
     *     description="Update an existing operator. Platform Operator only.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateOperatorRequest")
     *     ),
     *     @OA\Response(response=200, description="Operator updated", @OA\JsonContent(ref="#/components/schemas/Operator")),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Operator not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(UpdateOperatorRequest $request, string $id): JsonResponse
    {
        return $this->service->update($id, $request->validated());
    }

    /**
     * Delete an operator.
     *
     * @OA\Delete(
     *     path="/api/v0/operators/{id}",
     *     tags={"Operators - Platform Operator"},
     *     summary="Delete operator",
     *     description="Delete an operator. Platform Operator only.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\Response(response=200, description="Operator deleted", @OA\JsonContent(ref="#/components/schemas/SuccessResponse")),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Operator not found")
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        return $this->service->delete($id);
    }
}
