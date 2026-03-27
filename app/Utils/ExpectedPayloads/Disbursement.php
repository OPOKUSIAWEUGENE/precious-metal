<?php

namespace App\Utils\ExpectedPayloads;

/**
 * Standard request and response structures for Disbursement
 */
class Disbursement
{
    /**
     * Standard request payload structure
     */
    public static function request(): array
    {
        return [
            'amount' => 'numeric (required) - Amount to disburse',
            'currency' => 'string (required) - Currency code (e.g., GHS, NGN)',
            'fp_reference' => 'string (required) - FastPace reference',
            'gateway_code' => 'string (required) - The gateway identifier (e.g., flex_money, momo)',
            'client_reference' => 'string (optional) - Client reference',
            'source' => 'string (optional) - Source of the request',
            'narration' => 'string (optional) - Transaction narration/description',
            'sender' => 'array (required) - Sender details',
            'sender.email' => 'string (required) - Sender email',
            'sender.first_name' => 'string (required) - Sender first name',
            'sender.last_name' => 'string (required) - Sender last name',
            'sender.phone' => 'string (required) - Sender phone number',
            'recipient' => 'array (required) - Recipient details',
            'recipient.name' => 'string (required) - Recipient name',
            'recipient.email' => 'string (required) - Recipient email',
            'recipient.phone' => 'string (required) - Recipient phone',
            'recipient.account' => 'array (required) - Recipient account details',
            'recipient.account.payment_method_code' => 'string (required) - Payment method',
            'recipient.account.payment_channel_code' => 'string (required) - Payment channel',
            'recipient.account.account_number' => 'string (required) - Recipient account number',
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
            'data.reference' => 'string - Gateway transaction reference',
            'data.external_reference' => 'string - External reference from gateway',
            'data.status' => 'string - Transaction status',
            'data.amount' => 'numeric - Amount',
            'data.currency' => 'string - Currency',
            'data.can_retry' => 'boolean - Whether transaction can be retried',
            'data.can_switch_processor' => 'boolean - Whether can switch to another processor',
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
            'gateway_code' => 'flex_money',
            'client_reference' => 'CLT-2024-001',
            'source' => 'api',
            'narration' => 'Test disbursement',
            'sender' => [
                'email' => 'sender@example.com',
                'first_name' => 'John',
                'last_name' => 'Sender',
                'phone' => '+233123456789'
            ],
            'recipient' => [
                'name' => 'Jane Recipient',
                'email' => 'recipient@example.com',
                'phone' => '+233987654321',
                'account' => [
                    'payment_method_code' => 'bank',
                    'payment_channel_code' => 'bank-transfer',
                    'account_number' => '1234567890'
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
            'message' => 'Disbursement initiated successfully',
            'standard_message' => 'Success',
            'code' => '000',
            'data' => [
                'reference' => 'FP-2024-00001',
                'external_reference' => 'EXT-2024-001',
                'status' => 'pending',
                'amount' => 100.00,
                'currency' => 'GHS',
                'can_retry' => true,
                'can_switch_processor' => false
            ],
            'raw' => [
                'error' => 0,
                'message' => 'Disbursement initiated successfully',
                'transactionId' => 'EXT-2024-001',
                'status' => 'pending'
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
            'message' => 'Insufficient funds',
            'standard_message' => 'No sufficient funds',
            'code' => '116',
            'data' => [
                'reference' => 'FP-2024-00001',
                'external_reference' => null,
                'status' => 'failed',
                'amount' => 100.00,
                'currency' => 'GHS',
                'can_retry' => true,
                'can_switch_processor' => true
            ],
            'raw' => [
                'error' => 1,
                'message' => 'Insufficient funds'
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
            'message' => 'Transaction is being processed',
            'standard_message' => 'Pending',
            'code' => '3000',
            'data' => [
                'reference' => 'FP-2024-00001',
                'external_reference' => 'EXT-2024-001',
                'status' => 'pending',
                'amount' => 100.00,
                'currency' => 'GHS',
                'can_retry' => false,
                'can_switch_processor' => false
            ],
            'raw' => [
                'error' => 2,
                'message' => 'Transaction is being processed'
            ]
        ];
    }
}
