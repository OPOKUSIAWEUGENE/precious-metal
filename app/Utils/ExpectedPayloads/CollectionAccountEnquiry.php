<?php

namespace App\Utils\ExpectedPayloads;

/**
 * Standard request and response structures for Collection Account Enquiry
 */
class CollectionAccountEnquiry
{
    /**
     * Standard request payload structure
     */
    public static function request(): array
    {
        return [
            'gateway_code' => 'string (required) - The gateway identifier (e.g., momo, emerchant)',
            'payment_method_code' => 'string (required) - Payment method (e.g., mobile-money, card)',
            'payment_channel_code' => 'string (required) - Payment channel (e.g., mobile-money, card)',
            'account_number' => 'string (required) - Account number to verify',
            'client_reference' => 'string (optional) - Client reference for the transaction',
            'source' => 'string (required) - Source of the request',
        ];
    }

    /**
     * Standard response payload structure
     */
    public static function response(): array
    {
        return [
            'status' => 'string - Transaction status (success, failed, pending, error)',
            'message' => 'string - Gateway message',
            'standard_message' => 'string - Standardized FP message',
            'code' => 'string - Standard FP response code (e.g., 000, 100, 3000)',
            'data' => 'array (optional) - Response data',
            'data.account_name' => 'string - Verified account name',
            'data.account_number' => 'string - Account number',
            'data.is_valid' => 'boolean - Whether account is valid',
            'data.account_expiry' => 'string (optional) - Account expiry date',
            'raw' => 'array - Raw gateway response',
        ];
    }

    /**
     * Example request payload
     */
    public static function exampleRequest(): array
    {
        return [
            'gateway_code' => 'momo',
            'payment_method_code' => 'mobile-money',
            'payment_channel_code' => 'mobile-money',
            'account_number' => '+233123456789',
            'client_reference' => 'CLT-2024-001',
            'source' => 'api'
        ];
    }

    /**
     * Example success response
     */
    public static function exampleSuccessResponse(): array
    {
        return [
            'status' => 'success',
            'message' => 'Account verified successfully',
            'standard_message' => 'Success',
            'code' => '000',
            'data' => [
                'account_name' => 'JOHN DOE',
                'account_number' => '+233123456789',
                'is_valid' => true,
                'account_expiry' => '2025-12-31'
            ],
            'raw' => [
                'error' => 0,
                'message' => 'Account verified successfully',
                'accountName' => 'JOHN DOE',
                'accountNumber' => '+233123456789'
            ]
        ];
    }

    /**
     * Example failed response
     */
    public static function exampleFailedResponse(): array
    {
        return [
            'status' => 'failed',
            'message' => 'Invalid account number',
            'standard_message' => 'Rejected',
            'code' => '100',
            'data' => [
                'account_name' => null,
                'account_number' => '+233123456789',
                'is_valid' => false
            ],
            'raw' => [
                'error' => 1,
                'message' => 'Invalid account number'
            ]
        ];
    }
}
