<?php
declare(strict_types=1);

namespace Daylog\Presentation\Requests\Rules;

use Daylog\Application\Exceptions\TransportValidationException;

/**
 * Transport-level rule for ID presence and type.
 *
 * Purpose:
 * Provide reusable validation of the 'id' field at the transport boundary.
 * Ensures 'id' is present and is a string before business validation.
 *
 * Mechanics:
 * - Call IdTransportRule::assertValid($input).
 * - Throws TransportValidationException on violations.
 */
final class IdTransportRule
{
    /**
     * Assert that 'id' exists and is a string.
     *
     * @param array<string,mixed> $input Raw transport map.
     * @return void
     *
     * @throws TransportValidationException
     *         ID_REQUIRED   When 'id' key is missing or null.
     *         ID_NOT_STRING When 'id' exists but is not a string.
     */
    public static function assertValid(array $input): void
    {
        $rawId = $input['id'] ?? null;

        if ($rawId === null) {
            $errorCode = 'ID_REQUIRED';
            $exception = new TransportValidationException($errorCode);

            throw $exception;
        }

        if (!is_string($rawId)) {
            $errorCode = 'ID_NOT_STRING';
            $exception = new TransportValidationException($errorCode);
            
            throw $exception;
        }
    }
}
