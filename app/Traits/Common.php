<?php

namespace App\Traits;

use Exception;
use Illuminate\Support\Facades\Log;
use Throwable;

trait Common
{

    /**
     * log error to sentry and file
     *
     * @param Throwable $exception
     * @param string|null $callingClass
     * @return void
     */
    final protected static function logError(Throwable $exception, string $callingClass = null): void
    {
        Log::error($callingClass ?? __CLASS__, [$exception]);

    }

    /**
     * create a new folder 
     * 
     * @param string $path
     * @param int $permissions
     * @return bool
     */
    final protected static function createFolder(string $path, $permissions = 0777): bool
    {
        try {
            if (!file_exists($path)) {
                mkdir($path, $permissions, true);
            }
            return true;
        } catch (Exception $e) {
            Log::error(__CLASS__, [$e]);
        }
        return false;
    }


}