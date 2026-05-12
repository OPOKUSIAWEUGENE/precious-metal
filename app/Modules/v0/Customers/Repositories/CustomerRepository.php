<?php

namespace App\Modules\v0\Customers\Repositories;

use App\Modules\BaseRepository;
use App\Modules\v0\Customers\Interfaces\CustomerInterface;
use App\Modules\v0\Customers\Models\Customer;

class CustomerRepository extends BaseRepository implements CustomerInterface
{
    public function __construct()
    {
        parent::__construct(new Customer());
    }

    public function all()
    {
        return $this->model->all();
    }

    public function find(string $id): ?Customer
    {
        return $this->model->find($id);
    }

    public function create(array $data): Customer
    {
        return $this->model->create($data);
    }

    public function update(string $id, array $data): ?Customer
    {
        $customer = $this->find($id);
        if ($customer) {
            $customer->update($data);
            return $customer;
        }
        return null;
    }

    public function delete(string $id)
    {
        $customer = $this->find($id);
        if ($customer) {
            return $customer->delete();
        }
        return false;
    }

    public function findByEmail(string $email): ?Customer
    {
        return $this->model->where('email', $email)->first();
    }

    public function findByEmailIgnoreCase(string $email): ?Customer
    {
        return $this->model->whereRaw('LOWER(email) = ?', [strtolower($email)])->first();
    }

    public function findByPhoneNumber(string $phone): ?Customer
    {
        return $this->model->where('phone_number', $phone)->first();
    }
}
