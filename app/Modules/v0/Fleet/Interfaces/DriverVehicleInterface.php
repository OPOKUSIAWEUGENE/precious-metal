<?php

namespace App\Modules\v0\Fleet\Interfaces;

interface DriverVehicleInterface
{
    public function all();
    public function find(string $id);
    public function create(array $data);
    public function delete(string $id);
    public function findByProfileId(string $profileId);
    public function deleteByProfileId(string $profileId);
}