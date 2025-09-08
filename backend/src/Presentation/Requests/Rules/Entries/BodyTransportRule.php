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
    /**
     * Assert that 'body' exists and is a string (e.g., UC-1 AddEntry).
     *
     * @param array<string,mixed> $input
     * @return void
     *
     * @throws TransportValidationException
     */
    public static function assertRequired(array $input): void
    {
        $missing = 'BODY_REQUIRED';
        $type    = 'BODY_NOT_STRING';

        StringTransportRule::assertRequired($input, 'body', $missing, $type);
    }

    /**
     * Assert that 'body', if present, is a string (e.g., UC-5 UpdateEntry).
     *
     * @param array<string,mixed> $input
     * @return void
     *
     * @throws TransportValidationException
     */
    public static function assertOptional(array $input): void
    {
        $type = 'BODY_NOT_STRING';
        StringTransportRule::assertOptional($input, 'body', $type);
    }
}
