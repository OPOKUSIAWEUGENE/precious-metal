<?php

namespace App\Modules\v0\Fleets\Services;

use App\Modules\BaseService;
use App\Modules\v0\Fleet\Repositories\DriverVehicleRepository;
use App\Modules\v0\Fleet\Repositories\VehicleRepository;
use App\Modules\v0\User\Repositories\ProfileRepository;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use App\Modules\v0\Fleet\Resources\DriverVehicleResource;

class DriverVehicleService extends BaseService
{
    use ApiResponse;

    protected DriverVehicleRepository $assignmentRepository;
    protected VehicleRepository $vehicleRepository;
    protected ProfileRepository $driverProfileRepository;

    public function __construct(
        DriverVehicleRepository $assignmentRepository,
        VehicleRepository $vehicleRepository,
        ProfileRepository $driverProfileRepository
    ) {
        $this->assignmentRepository = $assignmentRepository;
        $this->vehicleRepository = $vehicleRepository;
        $this->driverProfileRepository = $driverProfileRepository;
    }

    public function all(?string $profileId = null): JsonResponse
    {
        if ($profileId) {
            $assignments = $this->assignmentRepository->findByProfileId($profileId);
        } else {
            $assignments = $this->assignmentRepository->all();
        }
        return self::success(DriverVehicleResource::collection($assignments));
    }

    public function find(string $id): JsonResponse
    {
        $assignment = $this->assignmentRepository->find($id);
        if (!$assignment) {
            return self::error('Driver vehicle assignment not found', code: 404);
        }
        return self::success(new DriverVehicleResource($assignment));
    }

    public function create(array $data): JsonResponse
    {
        $profile = $this->driverProfileRepository->find($data['profile_id']);
        if (!$profile) {
            return self::error('Driver profile not found: ' . $data['profile_id'], code: 404);
        }

        $vehicle = $this->vehicleRepository->find($data['vehicle_id']);
        if (!$vehicle) {
            return self::error('Vehicle not found: ' . $data['vehicle_id'], code: 404);
        }

        // Verify vehicle belongs to the driver's operator
        if ($vehicle->operator_id !== $profile->operator_id) {
            return self::error('Vehicle does not belong to the driver operator', code: 400);
        }

        $assignment = $this->assignmentRepository->create([
            'profile_id' => $profile->id,
            'vehicle_id' => $vehicle->id,
        ]);

        return self::success(new DriverVehicleResource($assignment), 201);
    }

    public function delete(string $id): JsonResponse
    {
        $deleted = $this->assignmentRepository->delete($id);
        if (!$deleted) {
            return self::error('Driver vehicle assignment not found or deletion failed', code: 404);
        }
        return self::success(['message' => 'Driver vehicle assignment deleted successfully']);
    }

    public function findByProfileId(string $profileId): JsonResponse
    {
        $assignments = $this->assignmentRepository->findByProfileId($profileId);
        return self::success(DriverVehicleResource::collection($assignments));
    }

    public function deleteByProfileId(string $profileId): JsonResponse
    {
        $deleted = $this->assignmentRepository->deleteByProfileId($profileId);
        return self::success(['message' => 'Driver vehicle assignments deleted successfully']);
    }
}