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
    /**
     * Assert that 'date' exists and is a string (e.g., UC-1 AddEntry).
     *
     * @param array<string,mixed> $input
     * @return void
     *
     * @throws TransportValidationException
     */
    public static function assertRequired(array $input): void
    {
        $missing = 'DATE_REQUIRED';
        $type    = 'DATE_NOT_STRING';

        StringTransportRule::assertRequired($input, 'date', $missing, $type);
    }

    /**
     * Assert that 'date', if present, is a string (e.g., UC-5 UpdateEntry).
     *
     * @param array<string,mixed> $input
     * @return void
     *
     * @throws TransportValidationException
     */
    public static function assertOptional(array $input): void
    {
        $type = 'DATE_NOT_STRING';
        StringTransportRule::assertOptional($input, 'date', $type);
    }
}
