<?php

namespace App\Utils\ExpectedPayloads;

/**
 * Standard request and response structures for Disbursement Account Enquiry
 */
class DisbursementAccountEnquiry
{
    /**
     * Standard request payload structure
     */
    public static function request(): array
    {
        return [
            'gateway_code' => 'string (required) - The gateway identifier (e.g., flex_money, momo)',
            'client_reference' => 'string (optional) - Client reference for the transaction',
            'source' => 'string (optional) - Source of the request',
            'account_details' => 'array (required) - Recipient account details',
            'account_details.payment_method_code' => 'string (required) - Payment method (e.g., bank, mobile-wallet)',
            'account_details.payment_channel_code' => 'string (required) - Payment channel (e.g., bank-transfer, mobile-money)',
            'account_details.account_number' => 'string (required) - Account number to verify',
            'account_details.country_code' => 'string (optional) - Country code (e.g., GH, NG)',
            'country_code' => 'string (optional) - Country code',
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
            'data.bank_name' => 'string - Bank name',
            'data.bank_code' => 'string - Bank code',
            'data.is_valid' => 'boolean - Whether account is valid',
            'raw' => 'array - Raw gateway response',
        ];
    }

    /**
     * Example request payload
     */
    public static function exampleRequest(): array
    {
        return [
            'gateway_code' => 'flex_money',
            'client_reference' => 'CLT-2024-001',
            'source' => 'api',
            'account_details' => [
                'payment_method_code' => 'bank',
                'payment_channel_code' => 'bank-transfer',
                'account_number' => '1234567890',
                'country_code' => 'GH'
            ],
            'country_code' => 'GH'
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
                'account_number' => '1234567890',
                'bank_name' => 'Sample Bank',
                'bank_code' => 'SMBL',
                'is_valid' => true
            ],
            'raw' => [
                'error' => 0,
                'message' => 'Account verified successfully',
                'accountName' => 'JOHN DOE',
                'accountNumber' => '1234567890'
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
                'account_number' => '1234567890',
                'is_valid' => false
            ],
            'raw' => [
                'error' => 1,
                'message' => 'Invalid account number'
            ]
        ];
    }
}
