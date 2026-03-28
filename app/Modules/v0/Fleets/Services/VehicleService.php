<?php

namespace App\Modules\v0\Fleets\Services;

use App\Modules\BaseService;
use App\Modules\v0\Fleet\Repositories\VehicleRepository;
use App\Modules\v0\Operator\Repositories\OperatorRepository;
use App\Modules\v0\Fleet\Enums\VehicleStatus;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use App\Modules\v0\Fleet\Resources\VehicleResource;

class VehicleService extends BaseService
{
    use ApiResponse;

    protected VehicleRepository $repository;
    protected OperatorRepository $operatorRepository;

    public function __construct(VehicleRepository $repository, OperatorRepository $operatorRepository)
    {
        $this->repository = $repository;
        $this->operatorRepository = $operatorRepository;
    }

    public function all(?string $operatorId = null): JsonResponse
    {
        if ($operatorId) {
            $vehicles = $this->repository->findAllByOperatorId($operatorId);
        } else {
            $vehicles = $this->repository->all();
        }
        return self::success(VehicleResource::collection($vehicles));
    }

    public function find(string $id): JsonResponse
    {
        $vehicle = $this->repository->find($id);
        if (!$vehicle) {
            return self::error('Vehicle not found', code: 404);
        }
        return self::success(new VehicleResource($vehicle));
    }

    public function create(array $data): JsonResponse
    {
        $operator = $this->resolveOperator($data['operator_id'] ?? null);
        
        $vehicleData = [
            'operator_id' => $operator->id,
            'fleet_identifier' => $data['fleet_identifier'],
            'type' => $data['type'],
            'plate_number' => $data['plate_number'],
            'capacity' => $data['capacity'],
            'status' => $data['status'] ?? VehicleStatus::ACTIVE,
            'created_by' => $data['created_by'] ?? $operator->id,
            'updated_by' => $data['updated_by'] ?? $operator->id,
        ];

        $vehicle = $this->repository->create($vehicleData);
        return self::success(new VehicleResource($vehicle), 201);
    }

    public function update(string $id, array $data): JsonResponse
    {
        $vehicle = $this->repository->find($id);
        if (!$vehicle) {
            return self::error('Vehicle not found', code: 404);
        }

        $updateData = [];
        
        if (isset($data['operator_id']) && $data['operator_id'] !== $vehicle->operator_id) {
            $operator = $this->resolveOperator($data['operator_id']);
            $updateData['operator_id'] = $operator->id;
        }
        
        if (isset($data['fleet_identifier'])) {
            $updateData['fleet_identifier'] = $data['fleet_identifier'];
        }
        
        if (isset($data['type'])) {
            $updateData['type'] = $data['type'];
        }
        
        if (isset($data['plate_number'])) {
            $updateData['plate_number'] = $data['plate_number'];
        }
        
        if (isset($data['capacity'])) {
            $updateData['capacity'] = $data['capacity'];
        }
        
        if (isset($data['status'])) {
            $updateData['status'] = $data['status'];
        }
        
        if (isset($data['updated_by'])) {
            $updateData['updated_by'] = $data['updated_by'];
        }

        $updatedVehicle = $this->repository->update($id, $updateData);
        return self::success(new VehicleResource($updatedVehicle), 200);
    }

    public function delete(string $id): JsonResponse
    {
        $deleted = $this->repository->delete($id);
        if (!$deleted) {
            return self::error('Vehicle not found or deletion failed', code: 404);
        }
        return self::success(['message' => 'Vehicle deleted successfully']);
    }

    public function findByFleetIdentifier(string $fleetIdentifier): JsonResponse
    {
        $vehicle = $this->repository->findByFleetIdentifier($fleetIdentifier);
        if (!$vehicle) {
            return self::error('Vehicle not found', code: 404);
        }
        return self::success(new VehicleResource($vehicle));
    }

    public function findAllByFleetIdentifiers(array $fleetIdentifiers): JsonResponse
    {
        $vehicles = $this->repository->findAllByFleetIdentifiers($fleetIdentifiers);
        return self::success(VehicleResource::collection($vehicles));
    }

    private function resolveOperator(?string $operatorId)
    {
        if (!$operatorId) {
            throw new \InvalidArgumentException('operatorId is required');
        }
        
        $operator = $this->operatorRepository->find($operatorId);
        if (!$operator) {
            throw new \InvalidArgumentException('Operator not found: ' . $operatorId);
        }
        
        return $operator;
    }
}