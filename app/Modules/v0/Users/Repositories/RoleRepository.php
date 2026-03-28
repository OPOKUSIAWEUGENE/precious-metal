<?php

namespace App\Modules\v0\Users\Repositories;

use App\Modules\BaseRepository;
use App\Modules\v0\Users\Interfaces\RoleInterface;
use App\Modules\v0\Users\Models\Role;

class RoleRepository extends BaseRepository implements RoleInterface
{
    public function __construct()
    {
        parent::__construct(new Role());
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
        $role = $this->find($id);
        if ($role) {
            $role->update($data);
            return $role;
        }
        return null;
    }

    public function delete(string $id)
    {
        $role = $this->find($id);
        if ($role) {
            return $role->delete();
        }
        return false;
    }

    public function findByName(string $name)
    {
        return $this->model->where('name', $name)->first();
    }
}