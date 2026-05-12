<?php

namespace App\Modules\v0\Customers\Services;

use App\Modules\BaseService;
use App\Modules\v0\Customers\Repositories\CustomerRepository;
use App\Modules\v0\Customers\Models\Customer;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use App\Modules\v0\Customers\Resources\CustomerResource;
use Illuminate\Support\Facades\Hash;

class CustomerService extends BaseService
{
    use ApiResponse;

    public function __construct(CustomerRepository $repository)
    {
        $this->repository = $repository;
    }

    public function all(): JsonResponse
    {
        $customers = $this->repository->all();
        return self::success(CustomerResource::collection($customers));
    }

    public function find(string $id): JsonResponse
    {
        $customer = $this->repository->find($id);
        if (!$customer) {
            return self::error('Customer not found', code: 404);
        }
        return self::success(new CustomerResource($customer));
    }

    public function create(array $data): JsonResponse
    {
        // Hash the password if provided
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        
        $customer = $this->repository->create($data);
        return self::success(new CustomerResource($customer), 201);
    }

    public function update(string $id, array $data): JsonResponse
    {
        // Hash the password if provided
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        
        $customer = $this->repository->update($id, $data);
        if (!$customer) {
            return self::error('Customer not found', code: 404);
        }
        return self::success(new CustomerResource($customer), 200);
    }

    public function delete(string $id): JsonResponse
    {
        $deleted = $this->repository->delete($id);
        if (!$deleted) {
            return self::error('Customer not found or deletion failed', code: 404);
        }
        return self::success(['message' => 'Customer deleted successfully']);
    }

    public function findByEmail(string $email): JsonResponse
    {
        $customer = $this->repository->findByEmail($email);
        if (!$customer) {
            return self::error('Customer not found', code: 404);
        }
        return self::success(new CustomerResource($customer));
    }

    /**
     * Map customer to response DTO.
     */
    public function mapToResponse(Customer $customer): array
    {
        return [
            'id' => $customer->id,
            'email' => $customer->email,
            'first_name' => $customer->first_name,
            'last_name' => $customer->last_name,
            'phone_number' => $customer->phone_number,
            'date_of_birth' => $customer->date_of_birth,
            'nationality' => $customer->nationality,
            'resident_address' => $customer->resident_address,
            'active' => $customer->active,
        ];
    }
}
