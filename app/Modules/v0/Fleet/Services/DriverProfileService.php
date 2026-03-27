<?php

namespace App\Modules\v0\Fleet\Services;

use App\Modules\v0\User\Enums\DriverStatus;
use App\Modules\v0\User\Enums\ProfileType;
use App\Modules\v0\User\Models\Profile;
use App\Modules\v0\User\Repositories\ProfileRepository;
use App\Modules\v0\User\Repositories\UserRepository;
use App\Modules\v0\Fleet\Repositories\VehicleRepository;
use App\Modules\v0\Fleet\Repositories\DriverVehicleRepository;
use App\Modules\v0\Operator\Repositories\OperatorRepository;
use App\Modules\v0\Fleet\Models\DriverVehicle;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class DriverProfileService
{
    use ApiResponse;

    protected ProfileRepository $profileRepository;
    protected VehicleRepository $vehicleRepository;
    protected DriverVehicleRepository $vehicleAssignmentRepository;
    protected OperatorRepository $operatorRepository;
    protected UserRepository $userRepository;

    public function __construct(
        ProfileRepository $profileRepository,
        VehicleRepository $vehicleRepository,
        DriverVehicleRepository $vehicleAssignmentRepository,
        OperatorRepository $operatorRepository,
        UserRepository $userRepository
    ) {
        $this->profileRepository = $profileRepository;
        $this->vehicleRepository = $vehicleRepository;
        $this->vehicleAssignmentRepository = $vehicleAssignmentRepository;
        $this->operatorRepository = $operatorRepository;
        $this->userRepository = $userRepository;
    }

    public function listDrivers(?string $operatorId = null): JsonResponse
    {
        if ($operatorId) {
            $profiles = $this->profileRepository->listByOperator($operatorId);
        } else {
            $profiles = $this->profileRepository->all();
        }
        
        // Filter to only driver profiles
        $driverProfiles = $profiles->filter(function($profile) {
            return $profile->profile_type === ProfileType::DRIVER;
        });

        return self::success($driverProfiles->values());
    }

    public function getDriver(string $id): JsonResponse
    {
        $profile = $this->profileRepository->find($id);
        if (!$profile || $profile->profile_type !== ProfileType::DRIVER) {
            return self::error('Driver profile not found', code: 404);
        }
        return self::success($profile);
    }

    public function createDriver(array $data): JsonResponse
    {
        try {
            $operator = $this->resolveOperator($data['operator_id']);
            $user = $this->ensureUser($data);
            $profile = $this->buildDriverProfile($data, $operator, $user);
            
            $savedProfile = $this->profileRepository->save($profile->toArray());
            
            // Assign vehicles if provided
            if (!empty($data['vehicle_identifiers'])) {
                $this->assignVehicles($savedProfile, $data['vehicle_identifiers']);
            }

            return self::success($savedProfile, 201);
        } catch (\InvalidArgumentException $e) {
            return self::error($e->getMessage(), code: 400);
        }
    }

    public function updateDriver(string $id, array $data): JsonResponse
    {
        try {
            $profile = $this->profileRepository->find($id);
            if (!$profile || $profile->profile_type !== ProfileType::DRIVER) {
                return self::error('Driver profile not found: ' . $id, code: 404);
            }

            // Update operator if changed
            if (isset($data['operator_id']) && $data['operator_id'] !== $profile->operator_id) {
                $operator = $this->resolveOperator($data['operator_id']);
                $profile->operator_id = $operator->id;
            }

            // Update user name
            if (!empty($data['name'])) {
                $profile->user->first_name = $data['name'];
                $profile->user->save();
            }

            // Update user contact
            if (!empty($data['email']) || !empty($data['phone'])) {
                $this->updateUserContact($profile->user, $data['email'] ?? null, $data['phone'] ?? null);
            }

            // Update license
            if (isset($data['license'])) {
                $profile->license = $data['license'];
            }

            // Update driver status
            if (isset($data['status'])) {
                $profile->driver_status = $data['status'];
            }

            if (isset($data['updated_by'])) {
                $profile->updated_by = $data['updated_by'];
            }

            $profile->save();

            // Update vehicle assignments
            if (isset($data['vehicle_identifiers'])) {
                $this->assignVehicles($profile, $data['vehicle_identifiers']);
            }

            return self::success($profile);
        } catch (\InvalidArgumentException $e) {
            return self::error($e->getMessage(), code: 400);
        }
    }

    private function buildDriverProfile(array $data, $operator, $user): Profile
    {
        $profile = new Profile();
        $profile->profile_type = ProfileType::DRIVER;
        $profile->role_id = null;
        $profile->operator_id = $operator->id;
        $profile->user_id = $user->id;
        $profile->active = true;
        $profile->current = true;
        $profile->license = $data['license'] ?? null;
        $profile->driver_status = $data['status'] ?? DriverStatus::AVAILABLE;
        $profile->created_by = $data['created_by'] ?? $user->id;
        $profile->updated_by = $data['updated_by'] ?? $user->id;

        return $profile;
    }

    private function assignVehicles(Profile $profile, array $vehicleIdentifiers): void
    {
        // Delete existing assignments
        $this->vehicleAssignmentRepository->deleteByProfileId($profile->id);

        if (empty($vehicleIdentifiers)) {
            return;
        }

        // Find vehicles by fleet identifiers
        $vehicles = $this->vehicleRepository->findAllByFleetIdentifiers($vehicleIdentifiers);
        $operatorId = $profile->operator_id;

        foreach ($vehicles as $vehicle) {
            // Only assign vehicles belonging to the same operator
            if ($vehicle->operator_id === $operatorId) {
                $assignment = new DriverVehicle();
                $assignment->profile_id = $profile->id;
                $assignment->vehicle_id = $vehicle->id;
                $assignment->save();
            }
        }
    }

    private function ensureUser(array $data): \App\Modules\v0\User\Models\User
    {
        $normalizedEmail = $this->normalizeEmail($data['email'] ?? null);
        
        // Try to find existing user
        $existingUser = $this->findUser($normalizedEmail, $data['phone'] ?? null);
        
        if ($existingUser) {
            $changed = false;
            
            // Update email if different
            if ($normalizedEmail && strtolower($normalizedEmail) !== strtolower($existingUser->email)) {
                $existingUser->email = $normalizedEmail;
                $changed = true;
            }
            
            // Update phone if different
            if (!empty($data['phone']) && $data['phone'] !== $existingUser->phone_number) {
                $existingUser->phone_number = $data['phone'];
                $changed = true;
            }
            
            // Update name if different
            if (!empty($data['name']) && $data['name'] !== $existingUser->first_name) {
                $existingUser->first_name = $data['name'];
                $changed = true;
            }
            
            if ($changed) {
                $existingUser->save();
            }
            
            return $existingUser;
        }

        // Create new user
        return $this->createUser($data, $normalizedEmail);
    }

    private function findUser(?string $email, ?string $phone): ?\App\Modules\v0\User\Models\User
    {
        if (!empty($email)) {
            $byEmail = $this->userRepository->findByEmailIgnoreCase($email);
            if ($byEmail) {
                return $byEmail;
            }
        }

        if (!empty($phone)) {
            return $this->userRepository->findByPhoneNumber($phone);
        }

        return null;
    }

    private function createUser(array $data, ?string $normalizedEmail): \App\Modules\v0\User\Models\User
    {
        $email = $normalizedEmail ?? $this->fallbackEmail($data);
        
        $userData = [
            'email' => $email,
            'phone_number' => $data['phone'] ?? null,
            'password' => Hash::make(Str::uuid()->toString()),
            'first_name' => $data['name'] ?? null,
            'active' => true,
        ];

        return $this->userRepository->create($userData);
    }

    private function updateUserContact($user, ?string $email, ?string $phone): void
    {
        $normalizedEmail = $this->normalizeEmail($email);
        $changed = false;

        if ($normalizedEmail && strtolower($normalizedEmail) !== strtolower($user->email)) {
            $user->email = $normalizedEmail;
            $changed = true;
        }

        if (!empty($phone) && $phone !== $user->phone_number) {
            $user->phone_number = $phone;
            $changed = true;
        }

        if ($changed) {
            $user->save();
        }
    }

    private function resolveOperator(string $operatorId)
    {
        $operator = $this->operatorRepository->find($operatorId);
        if (!$operator) {
            throw new \InvalidArgumentException('Operator not found: ' . $operatorId);
        }
        return $operator;
    }

    private function normalizeEmail(?string $email): ?string
    {
        if (empty($email)) {
            return null;
        }
        return trim(strtolower($email));
    }

    private function fallbackEmail(array $data): string
    {
        if (!empty($data['phone'])) {
            return $data['phone'] . '@drivers.rushhour.local';
        }
        return 'driver-' . Str::uuid() . '@drivers.rushhour.local';
    }
}