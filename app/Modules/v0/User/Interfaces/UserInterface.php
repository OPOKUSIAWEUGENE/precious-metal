<?php

namespace App\Modules\v0\User\Interfaces;

interface UserInterface
{
    public function all();
    public function find(string $id);
    public function create(array $data);
    public function update(string $id, array $data);
    public function delete(string $id);
    public function findByEmail(string $email);
    public function findByEmailIgnoreCase(string $email);
    public function findByPhoneNumber(string $phone);
}