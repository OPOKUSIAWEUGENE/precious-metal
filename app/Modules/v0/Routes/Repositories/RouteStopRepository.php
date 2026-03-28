<?php

namespace App\Modules\v0\Routes\Repositories;

use App\Modules\BaseRepository;
use App\Modules\v0\Routes\Interfaces\RouteStopInterface;
use App\Modules\v0\Routes\Models\RouteStop;

class RouteStopRepository extends BaseRepository implements RouteStopInterface
{
    public function __construct()
    {
        parent::__construct(new RouteStop());
    }

    public function findByName($name)
    {
        return $this->model->where('name', 'like', "%{$name}%")->get();
    }

    public function findByIds(array $ids)
    {
        return $this->model->whereIn('id', $ids)->get();
    }
}