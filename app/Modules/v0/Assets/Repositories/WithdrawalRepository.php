<?php

namespace App\Modules\v0\Assets\Repositories;

use App\Modules\BaseRepository;
use App\Modules\v0\Assets\Models\Withdrawal;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class WithdrawalRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new Withdrawal());
    }

    public function paginateAll(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with(['owner', 'wallet', 'rate', 'approver'])
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function findWithRelations(string $id): ?Withdrawal
    {
        return $this->model->newQuery()
            ->with(['owner', 'wallet', 'rate', 'approver'])
            ->find($id);
    }
}
