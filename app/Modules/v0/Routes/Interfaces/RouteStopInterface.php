<?php

namespace App\Modules\v0\Routes\Interfaces;

use App\Modules\BaseInterface;

interface RouteStopInterface extends BaseInterface
{
    public function findByName($name);
}