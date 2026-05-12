<?php

namespace App\Modules\v0\Customers\Interfaces;

use App\Modules\v0\Customers\Models\Customer;

interface CustomerInterface
{
    public function all();
    public function find(string $id): ?Customer;
    public function create(array $data): Customer;
    public function update(string $id, array $data): ?Customer;
    public function delete(string $id);
    public function findByEmail(string $email): ?Customer;
    public function findByEmailIgnoreCase(string $email): ?Customer;
    public function findByPhoneNumber(string $phone): ?Customer;
}
