<?php

namespace App\Utils;

use App\Modules\v0\UserModule\Models\User;


class CodeGenerator
{

    /**
     * generating a unique verification code
     * 
     * @return string 
     */
    public static function verificationCode(): string
    {
        $code = strval(rand(111111, 999999));
        if (User::where('verification_code', $code)->exists()) {
            self::verificationCode();
        }
        return $code;
    }

    /**
     * Summary of uniqueCodeGenerator
     * @param string $value
     * @return string
     */
    public static function uniqueCodeGenerator(string $value)
    {
        return strtolower(substr(str_replace([' ',',','-','_', '!', '@', '#'], '', $value), 0, 12) . (rand(1111, 9999)));
    }

}