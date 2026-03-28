<?php

namespace App\Modules\v0\Users\Repositories;

use App\Modules\BaseRepository;
use App\Modules\v0\Users\Interfaces\UserInterface;
use App\Modules\v0\Users\Models\User;

class UserRepository extends BaseRepository implements UserInterface
{
    public function __construct()
    {
        parent::__construct(new User());
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
        $user = $this->find($id);
        if ($user) {
            $user->update($data);
            return $user;
        }
        return null;
    }

    public function delete(string $id)
    {
        $user = $this->find($id);
        if ($user) {
            return $user->delete();
        }
        return false;
    }

    public function findByEmail(string $email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function findByEmailIgnoreCase(string $email)
    {
        return $this->model->whereRaw('LOWER(email) = ?', [strtolower($email)])->first();
    }

    public function findByPhoneNumber(string $phone)
    {
        return $this->model->where('phone_number', $phone)->first();
    }
}