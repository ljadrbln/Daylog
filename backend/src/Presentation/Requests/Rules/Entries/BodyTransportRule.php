<?php
declare(strict_types=1);

namespace Daylog\Presentation\Requests\Rules\Entries;

use Daylog\Application\Exceptions\TransportValidationException;
use Daylog\Presentation\Requests\Rules\Common\StringTransportRule;

/**
 * Transport-level rule for 'body' field.
 *
 * Purpose:
 * Provide semantic wrappers around StringTransportRule for the 'body' field.
 * Keeps the error codes consistent and the checks reusable.
 */
final class BodyTransportRule
{
    private const ERROR_REQUIRED   = 'BODY_REQUIRED';
    private const ERROR_NOT_STRING = 'BODY_MUST_BE_STRING';

    /**
     * Assert that 'body' exists and is a string (e.g., UC-1 AddEntry).
     *
     * @param array<string,mixed> $input
     * @return void
     *
     * @throws TransportValidationException
     */
    public static function assertValidRequired(array $input): void
    {
        $missingCode = self::ERROR_REQUIRED;
        $typeCode    = self::ERROR_NOT_STRING;

        StringTransportRule::assertValidRequired($input, 'body', $missingCode, $typeCode);
    }

    /**
     * Assert that 'body', if present, is a string (e.g., UC-5 UpdateEntry).
     *
     * @param array<string,mixed> $input
     * @return void
     *
     * @throws TransportValidationException
     */
    public static function assertValidOptional(array $input): void
    {
        $typeCode = self::ERROR_NOT_STRING;
        StringTransportRule::assertValidOptional($input, 'body', $typeCode);        
    }
}
