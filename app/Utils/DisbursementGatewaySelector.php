<?php

namespace App\Utils;

use App\Modules\v0\IntegrationModule\Ghipss\Services\GhipssFtcService;
use App\Modules\v0\IntegrationModule\Ghipss\Services\GhipssNecService;
use App\Modules\v0\IntegrationModule\Ghipss\Services\GhipssSqfService;
use App\Modules\v0\IntegrationModule\FlexMoney\Services\Balance\FlexBalanceService;
use App\Modules\v0\IntegrationModule\FlexMoney\Services\Payout\FlexPayoutService;
use App\Modules\v0\IntegrationModule\FlexMoney\Services\Payout\FlexStatusQueryService;
use App\Modules\v0\IntegrationModule\FlexMoney\Services\Verification\FlexAccountEnquiry;
use App\Modules\v0\IntegrationModule\MTN\Services\Disbursement\MTNBasicUserInfoService;
use App\Modules\v0\IntegrationModule\MTN\Services\Disbursement\MTNBalanceService;
use App\Modules\v0\IntegrationModule\MTN\Services\Disbursement\MTNTransferService;
use App\Modules\v0\IntegrationModule\MTN\Services\Disbursement\MTNTransferStatusService;
use App\Modules\v0\IntegrationModule\Opanet\Services\Wallet\AccountVerification\OpanetAccountEnquiry;
use App\Modules\v0\IntegrationModule\Opanet\Services\Wallet\OpanetBalanceEnquiryService;
use App\Modules\v0\IntegrationModule\Opanet\Services\Wallet\Payout\OpanetPayoutService;
use App\Modules\v0\IntegrationModule\Opanet\Services\Wallet\Payout\OpanetDisbursementCallbackService;
use App\Modules\v0\IntegrationModule\Opanet\Services\Wallet\TransactionLookup\OpanetTransactionLookupService;
use App\Modules\v0\IntegrationModule\Opanet\Services\Wallet\TransactionLookup\OpanetTransactionStatusService;
use App\Modules\v0\IntegrationModule\MTN\Services\Disbursement\MTNDisbursementCallbackService;

class DisbursementGatewaySelector
{
    /**
     * Get the appropriate payout/disbursement gateway service
     */
    public static function getPayoutGateway(array $input)
    {
        $gatewayCode = strtolower($input['gateway_code'] ?? '');

        if (empty($gatewayCode)) {
            throw new \Exception('gateway_code is required');
        }

        return match ($gatewayCode) {
            'ghipss' => app(GhipssFtcService::class, ['input' => $input]),
            'flexmoney' => app(FlexPayoutService::class, ['input' => $input]),
            'mtn' => app(MTNTransferService::class, ['input' => $input]),
            'opanet' => app(OpanetPayoutService::class)->payout($input),
            default => throw new \Exception("Unsupported gateway [$gatewayCode] for disbursement"),
        };
    }

    /**
     * Get the appropriate account enquiry/verification gateway service
     */
    public static function getAccountEnquiryGateway(array $request)
    {
        // Validate and extract gateway_code
        $gatewayCode = strtolower($request['gateway_code'] ?? '');

        if (empty($gatewayCode)) {
            throw new \Exception('gateway_code is required');
        }

        return match ($gatewayCode) {
            'ghipss' => app(GhipssNecService::class, ['request' => $request]),
            'mtn' => app(MTNBasicUserInfoService::class, ['request' => $request]),
            'flexmoney' => app(FlexAccountEnquiry::class)->accountVerification($request),
            'opanet' => app(OpanetAccountEnquiry::class)->accountVerification($request),
            default => throw new \Exception("Unsupported gateway [$gatewayCode] for account enquiry"),
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
            'ghipss' => app(GhipssSqfService::class, ['input' => $input]),
            'flexmoney' => app(FlexStatusQueryService::class, ['input' => $input]),
            'mtn' => app(MTNTransferStatusService::class, ['input' => $input]),
            'opanet' => app(OpanetTransactionStatusService::class, ['input' => $input]),
            default => throw new \Exception("Unsupported gateway [$gatewayCode] for status check"),
        };
    }

    /**
     * Get the appropriate account balance gateway service
     */
    public static function getAccountBalanceGateway(array $request)
    {
        $gatewayCode = strtolower($request['gateway_code'] ?? '');

        if (empty($gatewayCode)) {
            throw new \Exception('gateway_code is required');
        }

        return match ($gatewayCode) {
            'flexmoney' => app(FlexBalanceService::class, ['input' => $request]),
            'opanet' => app(OpanetBalanceEnquiryService::class, ['input' => $request]),
            'mtn' => app(MTNBalanceService::class, ['input' => $request]),
            default => throw new \Exception("Unsupported gateway [$gatewayCode] for account balance"),
        };
    }

    /**
     * Get the appropriate transaction lookup gateway service
     */
    public static function getTransactionLookupGateway(array $request)
    {
        $gatewayCode = strtolower($request['gateway_code'] ?? 'opanet');

        return match ($gatewayCode) {
            'opanet' => app(OpanetTransactionLookupService::class, [
                'input' => $request,
            ]),
            default => throw new \Exception("Unsupported gateway [$gatewayCode] for transaction lookup"),
        };
    }

    public static function getCallbackParser(string $gatewayCode)
    {
        $gatewayCode = strtolower($gatewayCode);

        return match ($gatewayCode) {
            'opanet' => app(OpanetDisbursementCallbackService::class),
            'mtn' => app(MTNDisbursementCallbackService::class),
            default => throw new \Exception("Unsupported gateway [$gatewayCode] for disbursement callbacks"),
        };
    }
}
