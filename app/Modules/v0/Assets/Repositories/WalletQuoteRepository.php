<?php

namespace App\Modules\v0\Assets\Repositories;

use App\Modules\BaseRepository;
use App\Modules\v0\Assets\Models\WalletQuote;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class WalletQuoteRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new WalletQuote());
    }

    public function paginateAll(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function findByReference(string $reference): ?WalletQuote
    {
        return $this->model->newQuery()
            ->where('reference', $reference)
            ->first();
    }
}
