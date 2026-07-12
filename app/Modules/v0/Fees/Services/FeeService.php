<?php

namespace App\Modules\v0\Fees\Services;

use App\Modules\BaseService;
use App\Modules\v0\Fees\Repositories\FeeRepository;
use App\Modules\v0\Fees\Resources\FeeResource;
use App\Modules\v0\Users\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FeeService extends BaseService
{
    use ApiResponse;

    public function __construct(FeeRepository $repository)
    {
        $this->repository = $repository;
    }

    public function all(int $perPage = 15): JsonResponse
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user instanceof User) {
            return self::error('Unauthorized', code: 401);
        }

        if (!$user->isPlatformAdmin()) {
            return self::error('Forbidden', code: 403);
        }

        return self::success(
            FeeResource::collection($this->repository->paginateAll($perPage))->response()->getData(true)
        );
    }

    public function setFee(array $data): JsonResponse
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user instanceof User) {
            return self::error('Unauthorized', code: 401);
        }

        if (!$user->isPlatformAdmin()) {
            return self::error('Forbidden', code: 403);
        }

        $fee = DB::transaction(function () use ($data) {
            $this->repository->deactivateActiveFees();

            return $this->repository->create([
                'fee' => $data['fee'],
                'status' => 'active',
                'effective_at' => $data['effective_at'] ?? now(),
                'notes' => $data['notes'] ?? null,
            ]);
        });

        return self::success(new FeeResource($fee), 201);
    }
}
