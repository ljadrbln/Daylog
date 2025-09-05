<?php
declare(strict_types=1);

namespace Daylog\Application\Validators\Rules;

use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Domain\Services\UuidGenerator;

/**
 * UUID validation rule for request DTOs.
 *
 * Purpose:
 * Centralize RFC-4122 UUID shape checks for Application validators.
 * Throws a domain-level validation error on violations.
 *
 * Mechanics:
 * - Call assertValid($id) from concrete UC validators.
 * - Delegates actual shape check to UuidGenerator::isValid().
 */
final class UuidIdRule
{
    /**
     * Assert that given id is a valid RFC-4122 UUID.
     *
     * @param string $id Non-empty string to verify.
     * @return void
     *
     * @throws DomainValidationException When the id is not a valid UUID.
     */
    public static function assertValid(string $id): void
    {
        $isValid = UuidGenerator::isValid($id);

        if ($isValid === false) {
            $errorCode = 'ID_INVALID';
            $exception = new DomainValidationException($errorCode);

            throw $exception;
        }
    }
}
