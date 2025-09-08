<?php
declare(strict_types=1);

namespace Daylog\Presentation\Requests\Rules\Common;

use Daylog\Application\Exceptions\TransportValidationException;
use Daylog\Presentation\Requests\Rules\Common\StringTransportRule;

/**
 * Transport-level rule for 'id' field.
 *
 * Purpose:
 * Provide semantic wrappers around StringTransportRule for the 'id' field.
 * UUID/format checks belong to domain/business validation, not transport.
 */
final class IdTransportRule
{
    private const ERROR_REQUIRED   = 'ID_REQUIRED';
    private const ERROR_NOT_STRING = 'ID_NOT_STRING';

    /**
     * Assert that 'id' exists and is a string (e.g., UC-3 GetEntry, UC-4 DeleteEntry).
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

        StringTransportRule::assertValidRequired($input, 'id', $missingCode, $typeCode);
    }
}
