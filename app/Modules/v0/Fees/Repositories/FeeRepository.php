<?php

namespace App\Modules\v0\Fees\Repositories;

use App\Modules\BaseRepository;
use App\Modules\v0\Fees\Models\Fee;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FeeRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new Fee());
    }

    public function paginateAll(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function deactivateActiveFees(): int
    {
        return $this->model->newQuery()
            ->where('status', 'active')
            ->update([
                'status' => 'inactive',
                'updated_at' => now(),
            ]);
    }
}
