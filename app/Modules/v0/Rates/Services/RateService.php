<?php

namespace App\Modules\v0\Rates\Services;

use App\Modules\BaseService;
use App\Modules\v0\Rates\Repositories\RateRepository;
use App\Modules\v0\Rates\Resources\RateResource;
use App\Modules\v0\Users\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RateService extends BaseService
{
    use ApiResponse;

    public function __construct(RateRepository $repository)
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
            RateResource::collection($this->repository->paginateAll($perPage))->response()->getData(true)
        );
    }

    public function setRate(array $data): JsonResponse
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user instanceof User) {
            return self::error('Unauthorized', code: 401);
        }

        if (!$user->isPlatformAdmin()) {
            return self::error('Forbidden', code: 403);
        }

        $rate = DB::transaction(function () use ($data) {
            $this->repository->deactivateActiveRates($data['rate_type']);

            return $this->repository->create([
                'rate' => $data['rate'],
                'rate_type' => $data['rate_type'],
                'status' => 'active',
                'effective_at' => $data['effective_at'] ?? now(),
                'notes' => $data['notes'] ?? null,
            ]);
        });

        return self::success(new RateResource($rate), 201);
    }
}
