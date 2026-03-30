<?php

namespace App\Modules\v0\Trips\Services;

use App\Modules\BaseService;
use App\Modules\v0\Trips\Repositories\BookingRepository;
use App\Modules\v0\Trips\Repositories\TripRepository;
use App\Modules\v0\User\Repositories\ProfileRepository;
use App\Modules\v0\Trips\Enums\BookingStatus;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use App\Modules\v0\Trips\Resources\BookingResource;

class BookingService extends BaseService
{
    use ApiResponse;

    protected TripRepository $tripRepository;
    protected ProfileRepository $profileRepository;

    public function __construct(
        BookingRepository $repository,
        TripRepository $tripRepository,
        ProfileRepository $profileRepository
    ) {
        $this->repository = $repository;
        $this->tripRepository = $tripRepository;
        $this->profileRepository = $profileRepository;
    }

    public function all(): JsonResponse
    {
        $bookings = $this->repository->all();
        return self::success(BookingResource::collection($bookings));
    }

    public function find(string $id): JsonResponse
    {
        $booking = $this->repository->find($id);
        if (!$booking) {
            return self::error('Booking not found', code: 404);
        }
        return self::success(new BookingResource($booking));
    }

    public function create(array $data): JsonResponse
    {
        // Validate trip exists
        $trip = $this->tripRepository->find($data['trip_id']);
        if (!$trip) {
            return self::error('Trip not found', code: 404);
        }

        // Validate passenger profile exists
        $passenger = $this->profileRepository->find($data['passenger_profile_id']);
        if (!$passenger) {
            return self::error('Passenger profile not found', code: 404);
        }

        $bookingData = [
            'trip_id' => $data['trip_id'],
            'passenger_profile_id' => $data['passenger_profile_id'],
            'seat_count' => $data['seat_count'] ?? 1,
            'status' => $data['status'] ?? BookingStatus::CONFIRMED,
        ];

        $booking = $this->repository->create($bookingData);
        return self::success(new BookingResource($booking), 201);
    }

    public function update(string $id, array $data): JsonResponse
    {
        // Validate trip if provided
        if (isset($data['trip_id'])) {
            $trip = $this->tripRepository->find($data['trip_id']);
            if (!$trip) {
                return self::error('Trip not found', code: 404);
            }
        }

        // Validate passenger if provided
        if (isset($data['passenger_profile_id'])) {
            $passenger = $this->profileRepository->find($data['passenger_profile_id']);
            if (!$passenger) {
                return self::error('Passenger profile not found', code: 404);
            }
        }

        $booking = $this->repository->update($id, $data);
        if (!$booking) {
            return self::error('Booking not found', code: 404);
        }
        return self::success(new BookingResource($booking), 200);
    }

    public function delete(string $id): JsonResponse
    {
        $deleted = $this->repository->delete($id);
        if (!$deleted) {
            return self::error('Booking not found or deletion failed', code: 404);
        }
        return self::success(['message' => 'Booking deleted successfully']);
    }

    public function findByTripId(string $tripId): JsonResponse
    {
        $bookings = $this->repository->findByTripId($tripId);
        return self::success(BookingResource::collection($bookings));
    }

    public function findByPassengerProfileId(string $passengerProfileId): JsonResponse
    {
        $bookings = $this->repository->findByPassengerProfileId($passengerProfileId);
        return self::success(BookingResource::collection($bookings));
    }
}