<?php

namespace App\Utils;

use App\Modules\v0\IntegrationModule\IDScan\Services\IDScanService;

class IdCardGatewaySelector
{
    /**
     * Get the appropriate ID Card verification gateway service
     */
    public static function getIdCardVerificationGateway(string $gateway_code)
    {
        return match (strtolower($gateway_code)) {
            'idscan' => new IDScanService(),
            default => throw new \Exception("Unsupported gateway [$gateway_code] for ID Card verification"),
        };
    }
}
