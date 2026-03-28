<?php

namespace App\Modules\v0\Finances\Interfaces;

interface SettlementInterface
{
    public function all();
    public function find(string $id);
    public function create(array $data);
    public function update(string $id, array $data);
    public function delete(string $id);
    public function findByOperator(string $operatorId);
    public function findByDriverProfile(string $driverProfileId);
    public function findByStatus($status);
    public function findByPeriod(string $periodStart, string $periodEnd);
}