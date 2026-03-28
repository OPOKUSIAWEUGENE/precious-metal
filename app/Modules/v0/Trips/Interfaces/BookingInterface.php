<?php

namespace App\Modules\v0\Trips\Interfaces;

interface BookingInterface
{
    public function all();
    public function find(string $id);
    public function create(array $data);
    public function update(string $id, array $data);
    public function delete(string $id);
    public function findByTripId(string $tripId);
    public function findByPassengerProfileId(string $passengerProfileId);
}