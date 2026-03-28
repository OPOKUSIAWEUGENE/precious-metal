<?php

namespace App\Modules\v0\Operators\Interfaces;



interface OperatorInterface
{
    public function all();
    public function find(string $id);
    public function create(array $data);
    public function update(string $id, array $data);
    public function delete(string $id);
}