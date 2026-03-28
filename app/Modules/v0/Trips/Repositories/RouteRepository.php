<?php

namespace App\Modules\v0\Trips\Repositories;

use App\Modules\BaseRepository;
use App\Modules\v0\Trips\Interfaces\RouteInterface;
use App\Modules\v0\Trips\Models\Route;

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

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(string $id, array $data)
    {
        $route = $this->find($id);
        if ($route) {
            $route->update($data);
            return $route;
        }
        return null;
    }

    public function delete(string $id)
    {
        $route = $this->find($id);
        if ($route) {
            return $route->delete();
        }
        return false;
    }
}