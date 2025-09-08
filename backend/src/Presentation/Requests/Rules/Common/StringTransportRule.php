<?php
declare(strict_types=1);

namespace Daylog\Presentation\Requests\Rules\Common;

use Daylog\Application\Exceptions\TransportValidationException;

/**
 * Transport-level rule for validating that a field is a string.
 *
 * Purpose:
 * Provide reusable assertions for presence (required) and type (string)
 * at the transport boundary, prior to business validation.
 *
 * Mechanics:
 * - assertRequired(): field key must exist, value !== null, and be string.
 * - assertOptional(): field may be absent/null; if present, must be string.
 * - Throws TransportValidationException with provided error codes.
 */
final class StringTransportRule
{
    /**
     * Assert that a required field exists and is a string.
     *
     * @param array<string,mixed> $input       Raw transport map.
     * @param string              $key         Field name (e.g., 'id', 'title').
     * @param string              $missingCode Error when missing or null (e.g., 'TITLE_REQUIRED').
     * @param string              $typeCode    Error when present but not a string (e.g., 'TITLE_NOT_STRING').
     * @return void
     *
     * @throws TransportValidationException
     */
    public static function assertRequired(array $input, string $key, string $missingCode, string $typeCode): void
    {
        $exists = array_key_exists($key, $input);
        $raw    = $exists ? $input[$key] : null;

        if ($raw === null) {
            $message   = $missingCode;
            $exception = new TransportValidationException($message);
            throw $exception;
        }

        if (!is_string($raw)) {
            $message   = $typeCode;
            $exception = new TransportValidationException($message);
            throw $exception;
        }
    }

    /**
     * Assert that an optional field, if present, is a string.
     *
     * @param array<string,mixed> $input    Raw transport map.
     * @param string              $key      Field name (e.g., 'title', 'body', 'date').
     * @param string              $typeCode Error when present but not a string (e.g., 'TITLE_NOT_STRING').
     * @return void
     *
     * @throws TransportValidationException
     */
    public static function assertOptional(array $input, string $key, string $typeCode): void
    {
        $exists = array_key_exists($key, $input);
        if ($exists === false) {
            return;
        }

        $raw = $input[$key];
        if ($raw === null) {
            return;
        }

        if (!is_string($raw)) {
            $message   = $typeCode;
            $exception = new TransportValidationException($message);
            throw $exception;
        }
    }
}
