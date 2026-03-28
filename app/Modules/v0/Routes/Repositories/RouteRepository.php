<?php

namespace App\Modules\v0\Routes\Repositories;

use App\Modules\BaseRepository;
use App\Modules\v0\Routes\Interfaces\RouteInterface;
use App\Modules\v0\Routes\Models\Route;

class RouteRepository extends BaseRepository implements RouteInterface
{
    public function __construct()
    {
        parent::__construct(new Route());
    }

    public function all()
    {
        return $this->model->with(['startStop', 'endStop'])->get();
    }

    public function find(string $id)
    {
        return $this->model->with(['startStop', 'endStop'])->find($id);
    }

    public function findByStatus($status)
    {
        return $this->model->where('status', $status)->with(['startStop', 'endStop'])->get();
    }
}