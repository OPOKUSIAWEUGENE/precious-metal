<?php

namespace App\Modules\v0\Trips\Repositories;

use App\Modules\BaseRepository;
use App\Modules\v0\Trips\Interfaces\BookingInterface;
use App\Modules\v0\Trips\Models\Booking;

class BookingRepository extends BaseRepository implements BookingInterface
{
    public function __construct()
    {
        parent::__construct(new Booking());
    }

    public function all()
    {
        return $this->model->with(['trip', 'passenger'])->get();
    }

    public function find(string $id)
    {
        return $this->model->with(['trip', 'passenger'])->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(string $id, array $data)
    {
        $booking = $this->find($id);
        if ($booking) {
            $booking->update($data);
            return $booking;
        }
        return null;
    }

    public function delete(string $id)
    {
        $booking = $this->find($id);
        if ($booking) {
            return $booking->delete();
        }
        return false;
    }

    public function findByTripId(string $tripId)
    {
        return $this->model->where('trip_id', $tripId)->get();
    }

    public function findByPassengerProfileId(string $passengerProfileId)
    {
        return $this->model->where('passenger_profile_id', $passengerProfileId)->get();
    }
}