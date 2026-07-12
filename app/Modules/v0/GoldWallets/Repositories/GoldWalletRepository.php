<?php

namespace App\Modules\v0\GoldWallets\Repositories;

use App\Modules\BaseRepository;
use App\Modules\v0\GoldWallets\Models\GoldWallet;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class GoldWalletRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new GoldWallet());
    }

    public function forUser(string $userId): Collection
    {
        return $this->model->newQuery()
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->get();
    }

    public function paginateAll(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with('user')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function forOwner(string $ownerId): Collection
    {
        return $this->model->newQuery()
            ->where('user_id', $ownerId)
            ->orderByDesc('created_at')
            ->get();
    }
}
