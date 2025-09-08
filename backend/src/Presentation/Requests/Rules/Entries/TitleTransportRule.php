<?php
declare(strict_types=1);

namespace Daylog\Presentation\Requests\Rules\Entries;

use Daylog\Application\Exceptions\TransportValidationException;
use Daylog\Presentation\Requests\Rules\Common\StringTransportRule;

/**
 * Transport-level rule for 'title' field.
 *
 * Purpose:
 * Provide semantic wrappers around StringTransportRule for the 'title' field,
 * keeping error codes centralized while reusing core checks.
 */
final class TitleTransportRule
{
    private const ERROR_REQUIRED   = 'TITLE_REQUIRED';
    private const ERROR_NOT_STRING = 'TITLE_MUST_BE_STRING';

    /**
     * Assert that 'title' exists and is a string (e.g., UC-1 AddEntry).
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

        StringTransportRule::assertValidRequired($input, 'title', $missingCode, $typeCode);
    }

    /**
     * Assert that 'title', if present, is a string (e.g., UC-5 UpdateEntry).
     *
     * @param array<string,mixed> $input
     * @return void
     *
     * @throws TransportValidationException
     */
    public static function assertValidOptional(array $input): void
    {
        $typeCode = self::ERROR_NOT_STRING;
        StringTransportRule::assertValidOptional($input, 'title', $typeCode);
    }
}
