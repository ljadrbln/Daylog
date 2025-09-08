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
    /**
     * Assert that 'title' exists and is a string (e.g., UC-1 AddEntry).
     *
     * @param array<string,mixed> $input
     * @return void
     *
     * @throws TransportValidationException
     */
    public static function assertRequired(array $input): void
    {
        $missing = 'TITLE_REQUIRED';
        $type    = 'TITLE_NOT_STRING';

        StringTransportRule::assertRequired($input, 'title', $missing, $type);
    }

    /**
     * Assert that 'title', if present, is a string (e.g., UC-5 UpdateEntry).
     *
     * @param array<string,mixed> $input
     * @return void
     *
     * @throws TransportValidationException
     */
    public static function assertOptional(array $input): void
    {
        $type = 'TITLE_NOT_STRING';
        StringTransportRule::assertOptional($input, 'title', $type);
    }
}
