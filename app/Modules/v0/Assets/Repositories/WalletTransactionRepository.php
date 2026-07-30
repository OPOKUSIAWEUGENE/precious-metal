<?php

namespace App\Modules\v0\Assets\Repositories;

use App\Modules\BaseRepository;
use App\Modules\v0\Assets\Models\WalletTransaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class WalletTransactionRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new WalletTransaction());
    }

    public function paginateAll(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function paginateForOwner(string $ownerType, string $ownerId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->where('owner_type', $ownerType)
            ->where('owner_id', $ownerId)
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function findByReference(string $reference): ?WalletTransaction
    {
        return $this->model->newQuery()
            ->where('reference', $reference)
            ->first();
    }
}
