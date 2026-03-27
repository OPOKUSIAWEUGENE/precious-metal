<?php

namespace App\Modules\v0\Fleet\Interfaces;

interface VehicleInterface
{
    public function all();
    public function find(string $id);
    public function create(array $data);
    public function update(string $id, array $data);
    public function delete(string $id);
    public function findAllByOperatorId(string $operatorId);
    public function findByFleetIdentifier(string $fleetIdentifier);
    public function findAllByFleetIdentifiers(array $fleetIdentifiers);
}