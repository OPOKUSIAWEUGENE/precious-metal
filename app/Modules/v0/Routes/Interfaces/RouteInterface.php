<?php

namespace App\Modules\v0\Routes\Interfaces;

use App\Modules\BaseInterface;

interface RouteInterface extends BaseInterface
{
    public function findByStatus($status);
}