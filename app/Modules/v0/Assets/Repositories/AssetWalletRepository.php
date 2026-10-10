<?php

namespace App\Modules\v0\Assets\Repositories;

use App\Modules\BaseRepository;
use App\Modules\v0\Assets\Models\AssetWallet;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class AssetWalletRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new AssetWallet());
    }

    public function forOwner(Authenticatable $owner): Collection
    {
        return $this->model->newQuery()
            ->with('owner')
            ->where('owner_type', $owner::class)
            ->where('owner_id', $owner->getAuthIdentifier())
            ->orderByDesc('created_at')
            ->get();
    }

    public function findForOwnerByReference(Authenticatable $owner, string $reference): ?AssetWallet
    {
        return $this->model->newQuery()
            ->where('wallet_reference', $reference)
            ->where('owner_type', $owner::class)
            ->where('owner_id', $owner->getAuthIdentifier())
            ->where('status', 'active')
            ->first();
    }

    public function paginateAll(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with('owner')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }
}
