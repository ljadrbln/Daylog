<?php
declare(strict_types=1);

namespace Daylog\Presentation\Requests\Rules\Entries;

use Daylog\Application\Exceptions\TransportValidationException;
use Daylog\Presentation\Requests\Rules\Common\StringTransportRule;

/**
 * Transport-level rule for 'date' field.
 *
 * Purpose:
 * Provide semantic wrappers around StringTransportRule for the 'date' field.
 * Note: format validation (e.g., YYYY-MM-DD) is not transport concern.
 */
final class DateTransportRule
{
    private const ERROR_REQUIRED   = 'DATE_REQUIRED';
    private const ERROR_NOT_STRING = 'DATE_MUST_BE_STRING';

    /**
     * Assert that 'date' exists and is a string (e.g., UC-1 AddEntry).
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

        StringTransportRule::assertValidRequired($input, 'date', $missingCode, $typeCode);
    }

    /**
     * Assert that 'date', if present, is a string (e.g., UC-5 UpdateEntry).
     *
     * @param array<string,mixed> $input
     * @return void
     *
     * @throws TransportValidationException
     */
    public static function assertValidOptional(array $input): void
    {
        $typeCode = self::ERROR_NOT_STRING;
        StringTransportRule::assertValidOptional($input, 'date', $typeCode);        
    }
}
