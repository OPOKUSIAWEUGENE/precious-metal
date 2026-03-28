<?php

namespace App\Modules\v0\Trips\Repositories;

use App\Modules\BaseRepository;
use App\Modules\v0\Trips\Interfaces\RouteStopInterface;
use App\Modules\v0\Trips\Models\RouteStop;

class RouteStopRepository extends BaseRepository implements RouteStopInterface
{
    public function __construct()
    {
        parent::__construct(new RouteStop());
    }

    public function all()
    {
        return $this->model->all();
    }

    public function find(string $id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(string $id, array $data)
    {
        $stop = $this->find($id);
        if ($stop) {
            $stop->update($data);
            return $stop;
        }
        return null;
    }

    public function delete(string $id)
    {
        $stop = $this->find($id);
        if ($stop) {
            return $stop->delete();
        }
        return false;
    }
}