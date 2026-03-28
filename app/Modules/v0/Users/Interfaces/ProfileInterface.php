<?php

namespace App\Modules\v0\Users\Interfaces;

interface ProfileInterface
{
    public function all();
    public function find(string $id);
    public function create(array $data);
    public function update(string $id, array $data);
    public function delete(string $id);
    public function findByUserId(string $userId);
    public function getCurrentProfile(string $userId);
    public function listByOperator(string $operatorId);
    public function save(array $data);
}