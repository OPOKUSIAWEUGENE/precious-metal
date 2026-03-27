<?php

namespace App\Utils;

use Illuminate\Support\Facades\Auth;

class SystemDetails
{

    const FP_MOBILE="mobile";
    const FP_PARTNER="partner";
    const FP_AGENT="agent";

    /**
     *@return object
     */
    public static function currentSystem(): object
    {
        return Auth::User()->select("system_type", "system_id")->get();
    }

}