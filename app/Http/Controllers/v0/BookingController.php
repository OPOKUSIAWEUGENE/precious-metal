<?php

namespace App\Http\Controllers\v0;

use App\Http\Controllers\Controller;
use App\Http\Requests\v0\Trips\StoreBookingRequest;
use App\Http\Requests\v0\Trips\UpdateBookingRequest;
use App\Modules\v0\Trips\Services\BookingService;
use Illuminate\Http\JsonResponse;

class BookingController extends Controller
{
    protected BookingService $service;

    public function __construct(BookingService $service)
    {
        $this->service = $service;
    }

    public function index(): JsonResponse
    {
        return $this->service->all();
    }

    public function store(StoreBookingRequest $request): JsonResponse
    {
        return $this->service->create($request->validated());
    }

    public function show(string $id): JsonResponse
    {
        return $this->service->find($id);
    }

    public function update(UpdateBookingRequest $request, string $id): JsonResponse
    {
        return $this->service->update($id, $request->validated());
    }

    public function destroy(string $id): JsonResponse
    {
        return $this->service->delete($id);
    }

    public function byTrip(string $tripId): JsonResponse
    {
        return $this->service->findByTripId($tripId);
    }

    public function byPassenger(string $passengerProfileId): JsonResponse
    {
        return $this->service->findByPassengerProfileId($passengerProfileId);
    }
}