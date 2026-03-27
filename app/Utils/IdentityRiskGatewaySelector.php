<?php

namespace App\Utils;

use App\Modules\v0\IntegrationModule\Dilisense\Services\DilisenseCheckIndividualService;
use App\Modules\v0\IntegrationModule\Dilisense\Services\DilisenseGenerateIndividualReportService;

class IdentityRiskGatewaySelector
{
    /**
     * Get the appropriate gateway for checking an individual
     */
    public static function getCheckIndividualGateway(string $gateway_code)
    {
        return match (strtolower($gateway_code)) {
            'dilisense' => new DilisenseCheckIndividualService(),
            default => throw new \Exception("Unsupported gateway [$gateway_code] for check individual"),
        };
    }

    /**
     * Get the appropriate gateway for generating individual report
     */
    public static function getGenerateReportGateway(string $gateway_code)
    {
        return match (strtolower($gateway_code)) {
            'dilisense' => new DilisenseGenerateIndividualReportService(),
            default => throw new \Exception("Unsupported gateway [$gateway_code] for generate individual report"),
        };
    }
}
