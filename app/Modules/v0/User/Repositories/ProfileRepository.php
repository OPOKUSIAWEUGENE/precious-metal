<?php

namespace App\Modules\v0\User\Repositories;

use App\Modules\BaseRepository;
use App\Modules\v0\User\Interfaces\ProfileInterface;
use App\Modules\v0\User\Models\Profile;

class ProfileRepository extends BaseRepository implements ProfileInterface
{
    public function __construct()
    {
        parent::__construct(new Profile());
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
        $profile = $this->find($id);
        if ($profile) {
            $profile->update($data);
            return $profile;
        }
        return null;
    }

    public function delete(string $id)
    {
        $profile = $this->find($id);
        if ($profile) {
            return $profile->delete();
        }
        return false;
    }

    public function findByUserId(string $userId)
    {
        return $this->model->where('user_id', $userId)->get();
    }

    public function getCurrentProfile(string $userId)
    {
        return $this->model->where('user_id', $userId)
            ->where('current', true)
            ->first();
    }
}