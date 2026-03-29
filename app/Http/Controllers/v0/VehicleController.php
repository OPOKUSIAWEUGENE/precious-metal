<?php

namespace App\Http\Controllers\v0;

use App\Http\Controllers\Controller;
use App\Http\Requests\v0\Fleet\StoreVehicleRequest;
use App\Http\Requests\v0\Fleet\UpdateVehicleRequest;
use App\Modules\v0\Fleet\Services\VehicleService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(schema="StoreVehicleRequest",
 *     required={"registration_number", "model", "make", "year", "capacity", "fleet_identifier"},
 *     @OA\Property(property="registration_number", type="string", example="GW-1234-20"),
 *     @OA\Property(property="model", type="string", example="Toyota Hiace"),
 *     @OA\Property(property="make", type="string", example="Toyota"),
 *     @OA\Property(property="year", type="integer", example=2020),
 *     @OA\Property(property="capacity", type="integer", example=25),
 *     @OA\Property(property="fleet_identifier", type="string", example="AMT-BUS-001"),
 *     @OA\Property(property="operator_id", type="string", format="uuid"),
 *     @OA\Property(property="status", type="string", enum={"ACTIVE", "MAINTENANCE", "INACTIVE"})
 * )
 * 
 * @OA\Schema(schema="UpdateVehicleRequest",
 *     @OA\Property(property="model", type="string"),
 *     @OA\Property(property="make", type="string"),
 *     @OA\Property(property="year", type="integer"),
 *     @OA\Property(property="capacity", type="integer"),
 *     @OA\Property(property="fleet_identifier", type="string"),
 *     @OA\Property(property="operator_id", type="string", format="uuid"),
 *     @OA\Property(property="status", type="string", enum={"ACTIVE", "MAINTENANCE", "INACTIVE"})
 * )
 */
class VehicleController extends Controller
{
    protected VehicleService $service;

    public function __construct(VehicleService $service)
    {
        $this->service = $service;
    }

    /**
     * List all vehicles.
     *
     * @OA\Get(
     *     path="/api/v0/vehicles",
     *     tags={"Vehicles - Client Operator"},
     *     summary="List all vehicles",
     *     description="Get a list of all vehicles. Client Operators can filter by operator_id.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="operator_id", in="query", required=false, @OA\Schema(type="string", format="uuid")),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Vehicle")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function index(): JsonResponse
    {
        $operatorId = request()->query('operator_id');
        return $this->service->all($operatorId);
    }

    /**
     * Create a new vehicle.
     *
     * @OA\Post(
     *     path="/api/v0/vehicles",
     *     tags={"Vehicles - Client Operator"},
     *     summary="Create vehicle",
     *     description="Add a new vehicle to the fleet. Client Operator only.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreVehicleRequest")
     *     ),
     *     @OA\Response(response=201, description="Vehicle created", @OA\JsonContent(ref="#/components/schemas/Vehicle")),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(StoreVehicleRequest $request): JsonResponse
    {
        return $this->service->create($request->validated());
    }

    /**
     * Get vehicle details.
     *
     * @OA\Get(
     *     path="/api/v0/vehicles/{id}",
     *     tags={"Vehicles - Client Operator"},
     *     summary="Get vehicle",
     *     description="Get details of a specific vehicle by ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\Response(response=200, description="Successful response", @OA\JsonContent(ref="#/components/schemas/Vehicle")),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Vehicle not found")
     * )
     */
    public function show(string $id): JsonResponse
    {
        return $this->service->find($id);
    }

    /**
     * Update a vehicle.
     *
     * @OA\Put(
     *     path="/api/v0/vehicles/{id}",
     *     tags={"Vehicles - Client Operator"},
     *     summary="Update vehicle",
     *     description="Update an existing vehicle. Client Operator only.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateVehicleRequest")
     *     ),
     *     @OA\Response(response=200, description="Vehicle updated", @OA\JsonContent(ref="#/components/schemas/Vehicle")),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Vehicle not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(UpdateVehicleRequest $request, string $id): JsonResponse
    {
        return $this->service->update($id, $request->validated());
    }

    /**
     * Delete a vehicle.
     *
     * @OA\Delete(
     *     path="/api/v0/vehicles/{id}",
     *     tags={"Vehicles - Client Operator"},
     *     summary="Delete vehicle",
     *     description="Delete a vehicle from the fleet. Client Operator only.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\Response(response=200, description="Vehicle deleted", @OA\JsonContent(ref="#/components/schemas/SuccessResponse")),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Vehicle not found")
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        return $this->service->delete($id);
    }

    /**
     * Get vehicle by fleet identifier.
     *
     * @OA\Get(
     *     path="/api/v0/vehicles/fleet/{fleetIdentifier}",
     *     tags={"Vehicles - Client Operator"},
     *     summary="Get vehicle by fleet identifier",
     *     description="Find a vehicle by its unique fleet identifier",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="fleetIdentifier", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Successful response", @OA\JsonContent(ref="#/components/schemas/Vehicle")),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Vehicle not found")
     * )
     */
    public function byFleetIdentifier(string $fleetIdentifier): JsonResponse
    {
        return $this->service->findByFleetIdentifier($fleetIdentifier);
    }
}