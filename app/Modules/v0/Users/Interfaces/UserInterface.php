<?php

namespace App\Modules\v0\Users\Interfaces;

use App\Modules\v0\Users\Models\User;

interface UserInterface
{
    public function all();
    public function find(string $id): ?User;
    public function create(array $data): User;
    public function update(string $id, array $data): ?User;
    public function delete(string $id);
    public function findByEmail(string $email): ?User;
    public function findByEmailIgnoreCase(string $email): ?User;
    public function findByPhoneNumber(string $phone): ?User;
}