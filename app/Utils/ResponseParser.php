<?php

namespace App\Utils;

use App\Modules\v0\IntegrationModule\Models\Integration;
use App\Modules\v0\TransactionModule\Models\FpDisbursement;
use App\Modules\v0\TransactionModule\Models\FpCollection;



use App\Modules\v0\SharedResourceModule\Enums\StatusEnum;

class ResponseParser
{




    public static function ParseTransferResponse($code, $data)
    {
        $response = [
            "id" => $data['transaction_id'],
            "status" => "failed"
        ];

        if (!in_array($code, Integration::HTTP_SUCCESS)) {
            return $response;
        }
        $response["status"] = $data["status"];
        return $response;
    }




    public static function parseExceptionResponse($e)
    {
        $resp = $e->getResponse();
        return [
            "errorCode" => $resp->getStatusCode(),
            "errorBody" => json_decode($resp->getBody()->getContents())
        ];

    }


    /**
     * Summary of commonPayoutResponse
     * @return array{can_retry: bool, can_switch_processor: bool, code: string, errors: array, external_reference: string, message: string, raw: array, standard_message: string, status: mixed}
     */
    public static function DisbursementResponseStructure(): array
    {
        return [
            'status' => FpDisbursement::STATUS_PENDING,
            'message' => '',
            'standard_message' => '',
            'code' => '',
            'raw' => [],
            'gateway_reference' => '',
            'can_retry' => false,
            'can_switch_processor' => false,
            'technical_response' => [],
        ];
    }

    /**
     * Standard response structure for Collection operations
     *
     * @return array{status: string, message: string, standard_message: string, code: string, raw: array, external_reference: string}
     */
    public static function CollectionResponseStructure(): array
    {
        return [
            'status' => FpCollection::STATUS_PENDING,
            'message' => '',
            'standard_message' => '',
            'code' => '',
            'raw' => [],
            'gateway_reference' => '',
            'technical_response' => [],
        ];
    }

    /**
     * Standard response structure for account enquiry/validation operations
     * 
     * @return array{is_valid: bool, account_name: string, account_number: string, payment_channel_code: string, payment_method_code: string, message: string, standard_message: string, code: string, raw: array}
     */
    public static function AccountEnquiryResponseStructure(): array
    {
        return [
            'status' => StatusEnum::ERROR->value,
            'account_name' => '',
            'message' => '',
            'code' => '',
            'raw' => [],
        ];
    }

    /**
     * Standard response structure for balance enquiry operations
     *
     * @return array{status: string, balance: float, currency: string, message: string, code: string, raw: array}
     */
    public static function BalanceResponseStructure(): array
    {
        return [
            'status' => StatusEnum::ERROR->value,
            'balance' => 0.0,
            'currency' => '',
            'message' => '',
            'code' => '',
            'raw' => [],
        ];
    }

    /**
     * Standard response structure for rate/forex enquiry operations
     *
     * @return array{status: string, rate: float|null, sending_currency: string, receiving_currency: string, expiry_time: string|null, message: string, code: string, raw: array}
     */
    public static function RateResponseStructure(): array
    {
        return [
            'status' => StatusEnum::ERROR->value,
            'rate' => null,
            'sending_currency' => '',
            'receiving_currency' => '',
            'expiry_time' => null,
            'message' => '',
            'code' => '',
            'raw' => [],
        ];
    }


    /**
     * Standard response structure for utility list operations (banks, countries, currency pairs, payout countries)
     *
     * @return array{status: string, message: string, code: string, data: array, raw: array}
     */
    public static function UtilityListResponseStructure(): array
    {
        return [
            'status' => StatusEnum::ERROR->value,
            'message' => '',
            'code' => '',
            'data' => [],
            'raw' => [],
        ];
    }

    /**
     * Standard response structure for Opanet operations (payout, transaction lookup, etc.)
     *
     * @return array{status: string, message: string, standard_message: string, code: string, raw: array}
     */
    public static function OpanetResponseStructure(): array
    {
        return [
            'status' => StatusEnum::ERROR->value,
            'message' => '',
            'standard_message' => '',
            'code' => '',
            'raw' => [],
        ];
    }

    /**
     * Decode JSON string safely
     *
     * @param mixed $value
     * @return array
     */
    public static function decodeJson(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        return [];
    }
}