<?php

namespace App\Modules\v0\Routes\Interfaces;


interface RouteInterface
{
    public function findByStatus($status);
}