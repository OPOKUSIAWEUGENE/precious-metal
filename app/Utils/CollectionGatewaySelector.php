<?php

namespace App\Utils;

use App\Modules\v0\IntegrationModule\EMerchant\Services\EMerchantConsumerService;
use App\Modules\v0\IntegrationModule\EMerchant\Services\EMerchantReconcileService;
use App\Modules\v0\IntegrationModule\EMerchant\Services\EMerchantWPFService;
use App\Modules\v0\IntegrationModule\MTN\Services\Collection\MTNBasicUserInfoService;
use App\Modules\v0\IntegrationModule\MTN\Services\Collection\MTNRequestToPayService;
use App\Modules\v0\IntegrationModule\MTN\Services\Collection\MTNRtpStatusService;

class CollectionGatewaySelector
{
    /**
     * Get the appropriate account enquiry gateway service
     */
    /**
     * Get the appropriate account enquiry gateway service
     */
    public static function getAccountEnquiryGateway(array $input)
    {
        $gatewayCode = strtolower($input['gateway_code'] ?? '');

        if (empty($gatewayCode)) {
            throw new \Exception('gateway_code is required');
        }

        return match ($gatewayCode) {
            'mtn' => new MTNBasicUserInfoService($input),
            // 'emerchant' => new EMerchantReconcileService($input),
            default => throw new \Exception("Unsupported gateway [$gatewayCode] for collection account enquiry"),
        };
    }

    /**
     * Get the appropriate collection/payment gateway service
     */
    public static function getCollectionGateway(array $input)
    {
        $gatewayCode = strtolower($input['gateway_code'] ?? '');

        if (empty($gatewayCode)) {
            throw new \Exception('gateway_code is required');
        }

        return match ($gatewayCode) {
            'mtn' => new MTNRequestToPayService($input),
            'emerchant' => new EMerchantWPFService($input),
            default => throw new \Exception("Unsupported gateway [$gatewayCode] for collection"),
        };
    }

    /**
     * Get the appropriate consumer action gateway service
     */
    public static function getConsumerGateway(array $input)
    {
        $gatewayCode = strtolower($input['gateway_code'] ?? '');

        if (empty($gatewayCode)) {
            throw new \Exception('gateway_code is required');
        }

        return match ($gatewayCode) {
            'emerchant' => new EMerchantConsumerService($input),
            default => throw new \Exception("Unsupported gateway [$gatewayCode] for consumer action"),
        };
    }

    /**
     * Get the appropriate status check gateway service
     */
    public static function getStatusCheckGateway(array $input)
    {
        $gatewayCode = strtolower($input['gateway_code'] ?? '');

        if (empty($gatewayCode)) {
            throw new \Exception('gateway_code is required');
        }

        return match ($gatewayCode) {
            'mtn' => new MTNRtpStatusService($input),
            'emerchant' => new EMerchantReconcileService($input),
            default => throw new \Exception("Unsupported gateway [$gatewayCode] for status check"),
        };
    }
}
