<?php

namespace App\Modules\v0\Rates\Repositories;

use App\Modules\BaseRepository;
use App\Modules\v0\Rates\Models\Rate;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class RateRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new Rate());
    }

    public function paginateAll(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function currentActive(?string $rateType = null): ?Rate
    {
        $query = $this->model->newQuery()
            ->where('status', 'active')
            ->orderByDesc('effective_at')
            ->orderByDesc('created_at');

        if ($rateType !== null) {
            $query->where('rate_type', $rateType);
        }

        return $query->first();
    }

    /**
     * @return Collection<int, Rate>
     */
    public function currentActiveByType(?string $rateType = null): Collection
    {
        $query = $this->model->newQuery()
            ->where('status', 'active')
            ->orderByDesc('effective_at')
            ->orderByDesc('created_at');

        if ($rateType !== null) {
            $query->where('rate_type', $rateType);
        }

        return $query->get()
            ->groupBy('rate_type')
            ->map(fn (Collection $rates) => $rates->first());
    }

    public function deactivateActiveRates(?string $rateType = null): int
    {
        $query = $this->model->newQuery()->where('status', 'active');

        if ($rateType !== null) {
            $query->where('rate_type', $rateType);
        }

        return $query->update([
            'status' => 'inactive',
            'updated_at' => now(),
        ]);
    }
}
