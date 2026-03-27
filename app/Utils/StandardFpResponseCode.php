<?php

namespace App\Utils;

use App\Modules\v0\SharedResourceModule\Enums\StatusEnum;

class StandardFpResponseCode
{
    // Standard FP response code constants
    public const CODE_000 = '000'; // Success

    // Codes that can be retried
    public const CAN_RETRY = [
        self::CODE_912, // Timeout
        self::CODE_996, // Upstream connection timeout
        self::CODE_902, // Connection not accepted
        self::CODE_304, // Duplicate record. Old record replaced.
    ];

    // Codes that can switch to another processor
    public const CAN_SWITCH_PROCESSOR = [
        self::CODE_909, // System defect
        self::CODE_991, // Upstream authorization failed
        self::CODE_911, // Timeout
        self::CODE_912, // Card issuer not available
        self::CODE_996, // Upstream connection timeout
        self::CODE_902, // Connection not accepted
        self::CODE_107, // Authorization failed / Token expired
        self::CODE_116, // No sufficient funds
        self::CODE_117, // Incorrect PIN
        self::CODE_119, // Cardholder transaction not permitted
        self::CODE_120, // Terminal transaction not permitted
        self::CODE_121, // Exceeds withdrawal frequency limit
        self::CODE_124, // Balance below minimum limit

    ];
    public const CODE_091 = '091'; // Institution unavailable
    public const CODE_100 = '100'; // Rejected
    public const CODE_107 = '107'; // Authorization failed / Token expired
    public const CODE_110 = '110'; // Invalid amount
    public const CODE_111 = '111'; // Invalid card number
    public const CODE_112 = '112'; // PIN element required for this transaction type
    public const CODE_114 = '114'; // Unknown card
    public const CODE_115 = '115'; // Function not available
    public const CODE_116 = '116'; // No sufficient funds
    public const CODE_117 = '117'; // Incorrect PIN
    public const CODE_118 = '118'; // No such card
    public const CODE_119 = '119'; // Cardholder transaction not permitted
    public const CODE_120 = '120'; // Terminal transaction not permitted
    public const CODE_121 = '121'; // Exceeds withdrawal frequency limit
    public const CODE_122 = '122'; // Security violation
    public const CODE_123 = '123'; // Withdrawal frequency exceeded
    public const CODE_124 = '124'; // Balance below minimum limit
    public const CODE_125 = '125'; // Card not in service
    public const CODE_126 = '126'; // Wrong PIN format
    public const CODE_127 = '127'; // Error PIN length
    public const CODE_128 = '128'; // Cryptographic error
    public const CODE_129 = '129'; // Suspected fraud / No Credit account
    public const CODE_130 = '130'; // Invalid recipient
    public const CODE_131 = '131'; // Recipient not registered for Mobile Money
    public const CODE_180 = '180'; // No Credit account
    public const CODE_181 = '181'; // No cheque account
    public const CODE_182 = '182'; // No saving Account
    public const CODE_183 = '183'; // Bad CVV
    public const CODE_184 = '184'; // Invalid date
    public const CODE_200 = '200'; // Rejected, Pick up card
    public const CODE_201 = '201'; // Expired card, Pick up card
    public const CODE_202 = '202'; // Fraud suspected, Pick up card
    public const CODE_204 = '204'; // Reserved usage, Pick up card
    public const CODE_205 = '205'; // Issuer call for Acquirer security service, pick up card
    public const CODE_206 = '206'; // Number of PIN validation attempts exceeded / Card lost, Pick up
    public const CODE_207 = '207'; // Special conditions, Pick up card
    public const CODE_209 = '209'; // Card stolen, Pick up
    public const CODE_210 = '210'; // Fraud suspected, Pick up
    public const CODE_280 = '280'; // Alternative
    public const CODE_299 = '299'; // Amount reversed
    public const CODE_300 = '300'; // Card pick up
    public const CODE_301 = '301'; // Successfully processed
    public const CODE_302 = '302'; // Not supported by receiver
    public const CODE_303 = '303'; // Unable to find record in the file
    public const CODE_304 = '304'; // Duplicate record. Old record replaced.
    public const CODE_305 = '305'; // Zone control error
    public const CODE_306 = '306'; // File locked
    public const CODE_307 = '307'; // Unsuccessful
    public const CODE_308 = '308'; // Format error
    public const CODE_309 = '309'; // Duplicate record. New record rejected
    public const CODE_381 = '381'; // File unknown
    public const CODE_382 = '382'; // Record not found
    public const CODE_383 = '383'; // Balance record cleared
    public const CODE_385 = '385'; // Balance request
    public const CODE_480 = '480'; // Reversal accepted
    public const CODE_481 = '481'; // Reversal accepted
    public const CODE_503 = '503'; // Invalid reversal amount
    public const CODE_581 = '581'; // Counters not available
    public const CODE_582 = '582'; // Reconciliation done
    public const CODE_800 = '800'; // Reconciliation process not available
    public const CODE_880 = '880'; // Cut-off in progress
    public const CODE_902 = '902'; // Invalid transaction
    public const CODE_908 = '908'; // Transaction receiver not referenced for switch
    public const CODE_909 = '909'; // System defect
    public const CODE_911 = '911'; // Timeout
    public const CODE_912 = '912'; // Card issuer not available
    public const CODE_990 = '990'; // Issuer not found
    public const CODE_991 = '991'; // Upstream authorization failed
    public const CODE_992 = '992'; // Transaction status unknown
    public const CODE_993 = '993'; // Issuer not found
    public const CODE_994 = '994'; // Transaction processing error
    public const CODE_995 = '995'; // Server processing error
    public const CODE_996 = '996'; // Upstream connection timeout
    public const CODE_3000 = '3000'; // Pending

    /**
     * Standard FP Response Codes Mapping (Code => Message)
     */
    public const STANDARD_FP_RESPONSE_MESSAGES = [
        self::CODE_000 => "Success",
        self::CODE_091 => "Institution unavailable",
        self::CODE_100 => "Rejected",
        self::CODE_107 => "Authorization failed / Token expired",
        self::CODE_110 => "Invalid amount",
        self::CODE_111 => "Invalid card number",
        self::CODE_112 => "PIN element required for this transaction type",
        self::CODE_114 => "Invalid Account",
        self::CODE_115 => "Function not available",
        self::CODE_116 => "No sufficient funds",
        self::CODE_117 => "Incorrect PIN",
        self::CODE_118 => "No such card",
        self::CODE_119 => "Cardholder transaction not permitted",
        self::CODE_120 => "Terminal transaction not permitted",
        self::CODE_121 => "Exceeds withdrawal frequency limit ",
        self::CODE_122 => "Security violation",
        self::CODE_123 => "Withdrawal frequency exceeded",
        self::CODE_124 => "Balance below minimum limit",
        self::CODE_125 => "Card not in service",
        self::CODE_126 => "Wrong PIN format",
        self::CODE_127 => "Error PIN length",
        self::CODE_128 => "Cryptographic error",
        self::CODE_129 => "Suspected fraud / No Credit account",
        self::CODE_130 => "Invalid recipient",
        self::CODE_131 => "Recipient not registered for Mobile Money",
        self::CODE_180 => "No Credit account",
        self::CODE_181 => "No cheque account",
        self::CODE_182 => "No saving Account",
        self::CODE_183 => "Bad CVV",
        self::CODE_184 => "Invalid date",
        self::CODE_200 => "Rejected, Pick up card",
        self::CODE_201 => "Expired card, Pick up card",
        self::CODE_202 => "Fraud suspected, Pick up card",
        self::CODE_204 => "Reserved usage, Pick up card",
        self::CODE_205 => "Issuer call for Acquirer security service, pick up card",
        self::CODE_206 => "Number of PIN validation attempts exceeded / Card lost, Pick up",
        self::CODE_207 => "Special conditions, Pick up card",
        self::CODE_209 => "Card stolen, Pick up",
        self::CODE_210 => "Fraud suspected, Pick up",
        self::CODE_280 => "Alternative",
        self::CODE_299 => "Amount reversed",
        self::CODE_300 => "Card pick up",
        self::CODE_301 => "Successfully processed",
        self::CODE_302 => "Not supported by receiver",
        self::CODE_303 => "Unable to find record in the file",
        self::CODE_304 => "Duplicate record. Old record replaced.",
        self::CODE_305 => "Zone control error",
        self::CODE_306 => "File locked",
        self::CODE_307 => "Unsuccessful",
        self::CODE_308 => "Format error",
        self::CODE_309 => "Duplicate record. New record rejected",
        self::CODE_381 => "File unknown",
        self::CODE_382 => "Record not found",
        self::CODE_383 => "Balance record cleared",
        self::CODE_385 => "Balance request",
        self::CODE_480 => "Reversal accepted",
        self::CODE_481 => "Reversal accepted",
        self::CODE_503 => "Invalid reversal amount",
        self::CODE_581 => "Counters not available",
        self::CODE_582 => "Reconciliation done",
        self::CODE_800 => "Reconciliation process not available",
        self::CODE_880 => "Cut-off in progress",
        self::CODE_902 => "Invalid transaction",
        self::CODE_908 => "Transaction receiver not referenced for switch",
        self::CODE_909 => "System defect",
        self::CODE_911 => "Timeout",
        self::CODE_912 => "Card issuer not available",
        self::CODE_990 => "Issuer not found",
        self::CODE_991 => "Upstream authorization failed",
        self::CODE_992 => "Transaction status unknown",
        self::CODE_993 => "Issuer not found",
        self::CODE_994 => "Transaction processing error",
        self::CODE_995 => "Server processing error",
        self::CODE_996 => "Upstream connection timeout",

            // System Standard Codes (Essential for gateway operation)
        self::CODE_3000 => "Pending",
    ];

    /**
     * Map standard FP response codes to internal status values.
     * Codes not explicitly listed here default to FAILED in getStatus().
     */
    public const STANDARD_FP_CODE_TO_STATUS_MAPPING = [
            // Success states
        self::CODE_000 => StatusEnum::PROCESSED->value,
        self::CODE_301 => StatusEnum::PROCESSED->value,
        self::CODE_480 => StatusEnum::PROCESSED->value,
        self::CODE_481 => StatusEnum::PROCESSED->value,

            // Pending / Unknown states
        self::CODE_909 => StatusEnum::PENDING->value,
        self::CODE_911 => StatusEnum::PENDING->value,
        self::CODE_912 => StatusEnum::PENDING->value,
        self::CODE_990 => StatusEnum::PENDING->value,
        self::CODE_992 => StatusEnum::PENDING->value,
        self::CODE_995 => StatusEnum::PENDING->value,
        self::CODE_996 => StatusEnum::PENDING->value,
        self::CODE_3000 => StatusEnum::PENDING->value,

            // Failed states - 0xx
        self::CODE_091 => StatusEnum::FAILED->value,

            // Failed states - 1xx
        self::CODE_100 => StatusEnum::FAILED->value,
        self::CODE_107 => StatusEnum::FAILED->value,
        self::CODE_110 => StatusEnum::FAILED->value,
        self::CODE_111 => StatusEnum::FAILED->value,
        self::CODE_112 => StatusEnum::FAILED->value,
        self::CODE_114 => StatusEnum::FAILED->value,
        self::CODE_115 => StatusEnum::FAILED->value,
        self::CODE_116 => StatusEnum::FAILED->value,
        self::CODE_117 => StatusEnum::FAILED->value,
        self::CODE_118 => StatusEnum::FAILED->value,
        self::CODE_119 => StatusEnum::FAILED->value,
        self::CODE_120 => StatusEnum::FAILED->value,
        self::CODE_121 => StatusEnum::FAILED->value,
        self::CODE_122 => StatusEnum::FAILED->value,
        self::CODE_123 => StatusEnum::FAILED->value,
        self::CODE_124 => StatusEnum::FAILED->value,
        self::CODE_125 => StatusEnum::FAILED->value,
        self::CODE_126 => StatusEnum::FAILED->value,
        self::CODE_127 => StatusEnum::FAILED->value,
        self::CODE_128 => StatusEnum::FAILED->value,
        self::CODE_129 => StatusEnum::FAILED->value,
        self::CODE_130 => StatusEnum::FAILED->value,
        self::CODE_131 => StatusEnum::FAILED->value,
        self::CODE_180 => StatusEnum::FAILED->value,
        self::CODE_181 => StatusEnum::FAILED->value,
        self::CODE_182 => StatusEnum::FAILED->value,
        self::CODE_183 => StatusEnum::FAILED->value,
        self::CODE_184 => StatusEnum::FAILED->value,

            // Failed states - 2xx
        self::CODE_200 => StatusEnum::FAILED->value,
        self::CODE_201 => StatusEnum::FAILED->value,
        self::CODE_202 => StatusEnum::FAILED->value,
        self::CODE_204 => StatusEnum::FAILED->value,
        self::CODE_205 => StatusEnum::FAILED->value,
        self::CODE_206 => StatusEnum::FAILED->value,
        self::CODE_207 => StatusEnum::FAILED->value,
        self::CODE_209 => StatusEnum::FAILED->value,
        self::CODE_210 => StatusEnum::FAILED->value,
        self::CODE_280 => StatusEnum::FAILED->value,
        self::CODE_299 => StatusEnum::FAILED->value,

            // Failed states - 3xx
        self::CODE_300 => StatusEnum::FAILED->value,
        self::CODE_302 => StatusEnum::FAILED->value,
        self::CODE_303 => StatusEnum::FAILED->value,
        self::CODE_304 => StatusEnum::FAILED->value,
        self::CODE_305 => StatusEnum::FAILED->value,
        self::CODE_306 => StatusEnum::FAILED->value,
        self::CODE_307 => StatusEnum::FAILED->value,
        self::CODE_308 => StatusEnum::FAILED->value,
        self::CODE_309 => StatusEnum::FAILED->value,
        self::CODE_381 => StatusEnum::FAILED->value,
        self::CODE_382 => StatusEnum::FAILED->value,
        self::CODE_383 => StatusEnum::FAILED->value,
        self::CODE_385 => StatusEnum::FAILED->value,

            // Failed states - 5xx & 8xx
        self::CODE_503 => StatusEnum::FAILED->value,
        self::CODE_581 => StatusEnum::FAILED->value,
        self::CODE_582 => StatusEnum::FAILED->value,
        self::CODE_800 => StatusEnum::FAILED->value,
        self::CODE_880 => StatusEnum::FAILED->value,

            // Failed states - 9xx
        self::CODE_902 => StatusEnum::FAILED->value,
        self::CODE_908 => StatusEnum::FAILED->value,
        self::CODE_991 => StatusEnum::FAILED->value,
        self::CODE_993 => StatusEnum::FAILED->value,
        self::CODE_994 => StatusEnum::FAILED->value,
    ];

    /**
     * Get message for a standard FP response code.
     *
     * @param string|int|null $code
     * @return string
     */
    public static function getMessage($code): string
    {
        return self::STANDARD_FP_RESPONSE_MESSAGES[(string) $code] ?? "Unknown Response Code: " . $code;
    }

    /**
     * Get the standardized status value mapped to a Standard FP response code.
     *
     * @param string|int|null $code
     * @return string
     */
    public static function getStatus($code): string
    {
        if ($code === null) {
            return StatusEnum::FAILED->value;
        }

        return self::STANDARD_FP_CODE_TO_STATUS_MAPPING[(string) $code] ?? StatusEnum::FAILED->value;
    }

    /**
     * Check if a response code allows retry.
     *
     * @param string|int|null $code
     * @return bool
     */
    public static function canRetry($code): bool
    {
        return in_array((string) $code, self::CAN_RETRY, true);
    }

    /**
     * Check if a response code allows switching to another processor.
     *
     * @param string|int|null $code
     * @return bool
     */
    public static function canSwitchProcessor($code): bool
    {
        return in_array((string) $code, self::CAN_SWITCH_PROCESSOR, true);
    }
}
