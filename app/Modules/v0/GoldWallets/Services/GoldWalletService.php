<?php

namespace App\Modules\v0\GoldWallets\Services;

use App\Modules\BaseService;
use App\Modules\v0\GoldWallets\Repositories\GoldWalletRepository;
use App\Modules\v0\GoldWallets\Resources\GoldWalletResource;
use App\Modules\v0\Users\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class GoldWalletService extends BaseService
{
    use ApiResponse;

    public function __construct(GoldWalletRepository $repository)
    {
        $this->repository = $repository;
    }

    public function myWallets(): JsonResponse
    {
        /** @var Authenticatable|null $user */
        $user = Auth::user();

        if (!$user) {
            return self::error('Unauthorized', code: 401);
        }

        return self::success(
            GoldWalletResource::collection($this->repository->forOwner((string) $user->getAuthIdentifier()))->response()->getData(true)
        );
    }

    public function allWallets(int $perPage = 15): JsonResponse
    {
        /** @var Authenticatable|null $user */
        $user = Auth::user();

        if (!$user instanceof User) {
            return self::error('Unauthorized', code: 401);
        }

        if (!$user->isPlatformAdmin()) {
            return self::error('Forbidden', code: 403);
        }

        return self::success(
            GoldWalletResource::collection($this->repository->paginateAll($perPage))->response()->getData(true)
        );
    }
}
