<?php

namespace App\Modules\v0\Operators\Repositories;

use App\Modules\v0\Operator\Interfaces\OperatorInterface;
use App\Modules\BaseRepository;
use App\Modules\v0\Operator\Models\Operator;

class OperatorRepository extends BaseRepository implements OperatorInterface
{
	public function __construct()
    {
		 parent::__construct(new Operator());
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
        $operator = $this->find($id);
        if ($operator) {
            $operator->update($data);
            return $operator;
        }
        return null;
    }

    public function delete(string $id)
    {
        $operator = $this->find($id);
        if ($operator) {
            return $operator->delete();
        }
        return false;
    }
}