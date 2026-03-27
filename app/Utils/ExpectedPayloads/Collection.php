<?php

namespace App\Utils\ExpectedPayloads;

/**
 * Standard request and response structures for Collection
 */
class Collection
{
    /**
     * Standard request payload structure
     */
    public static function request(): array
    {
        return [
            'amount' => 'numeric (required) - Amount to collect',
            'currency' => 'string (required) - Currency code (e.g., GHS, NGN)',
            'fp_reference' => 'string (required) - FastPace reference',
            'narration' => 'string (optional) - Transaction narration',
            'note' => 'string (optional) - Additional note',
            'gateway_code' => 'string (required) - The gateway identifier (e.g., momo, emerchant)',
            'client_reference' => 'string (required) - Client reference',
            'customer' => 'array (required) - Customer details',
            'customer.email' => 'string (optional) - Customer email',
            'customer.first_name' => 'string (optional) - Customer first name',
            'customer.last_name' => 'string (optional) - Customer last name',
            'customer.phone' => 'string (optional) - Customer phone number',
            'customer.account_details' => 'array (required) - Customer account details',
            'customer.account_details.payment_method_code' => 'string (required) - Payment method',
            'customer.account_details.payment_channel_code' => 'string (required) - Payment channel',
            'customer.account_details.account_number' => 'string (required) - Account number',
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
            'data.reference' => 'string - FastPace reference',
            'data.external_reference' => 'string - External gateway reference',
            'data.status' => 'string - Transaction status',
            'data.amount' => 'numeric - Amount',
            'data.currency' => 'string - Currency',
            'data.checkout_url' => 'string (optional) - Checkout URL for redirect',
            'data.token' => 'string (optional) - Payment token',
            'raw' => 'array - Raw gateway response',
        ];
    }

    /**
     * Example request payload
     */
    public static function exampleRequest(): array
    {
        return [
            'amount' => 100.00,
            'currency' => 'GHS',
            'fp_reference' => 'FP-2024-00001',
            'narration' => 'Test payment',
            'note' => 'Payment for order #12345',
            'gateway_code' => 'momo',
            'client_reference' => 'CLT-2024-001',
            'customer' => [
                'email' => 'customer@example.com',
                'first_name' => 'John',
                'last_name' => 'Customer',
                'phone' => '+233123456789',
                'account_details' => [
                    'payment_method_code' => 'mobile-money',
                    'payment_channel_code' => 'mobile-money',
                    'account_number' => '+233123456789'
                ]
            ]
        ];
    }

    /**
     * Example success response
     */
    public static function exampleSuccessResponse(): array
    {
        return [
            'status' => 'success',
            'message' => 'Payment initiated successfully',
            'standard_message' => 'Success',
            'code' => '000',
            'data' => [
                'reference' => 'FP-2024-00001',
                'external_reference' => 'EXT-2024-001',
                'status' => 'pending',
                'amount' => 100.00,
                'currency' => 'GHS',
                'checkout_url' => 'https://checkout.example.com/pay/EXT-2024-001',
                'token' => 'tok_abc123'
            ],
            'raw' => [
                'error' => 0,
                'message' => 'Payment initiated successfully',
                'transactionId' => 'EXT-2024-001',
                'status' => 'pending',
                'checkoutUrl' => 'https://checkout.example.com/pay/EXT-2024-001',
                'token' => 'tok_abc123'
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
            'message' => 'Payment declined',
            'standard_message' => 'Rejected',
            'code' => '100',
            'data' => [
                'reference' => 'FP-2024-00001',
                'external_reference' => null,
                'status' => 'failed',
                'amount' => 100.00,
                'currency' => 'GHS'
            ],
            'raw' => [
                'error' => 1,
                'message' => 'Payment declined'
            ]
        ];
    }

    /**
     * Example pending response
     */
    public static function examplePendingResponse(): array
    {
        return [
            'status' => 'pending',
            'message' => 'Payment is being processed',
            'standard_message' => 'Pending',
            'code' => '3000',
            'data' => [
                'reference' => 'FP-2024-00001',
                'external_reference' => 'EXT-2024-001',
                'status' => 'pending',
                'amount' => 100.00,
                'currency' => 'GHS'
            ],
            'raw' => [
                'error' => 2,
                'message' => 'Payment is being processed'
            ]
        ];
    }
}
