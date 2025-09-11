<?php
declare(strict_types=1);

namespace Daylog\Application\Validators\Rules\Common;

use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Domain\Services\UuidGenerator;

/**
 * UUID validation rule for request DTOs.
 *
 * Purpose:
 * Validate that an entry id is present (non-empty) and a valid RFC-4122 UUID v4.
 * Throws a domain-level validation error on violations.
 *
 * Mechanics:
 * - Call assertValid($id) from concrete UC validators (Update/Get/Delete).
 * - Presentation guarantees non-null string; sanitizers already trim.
 */
final class IdRule
{
    /**
     * Assert that given id is non-empty and a valid UUID v4.
     *
     * @param string $id Raw id from request DTO.
     * @return void
     *
     * @throws DomainValidationException When id is empty → ID_REQUIRED,
     *                                   or not a UUID v4 → ID_INVALID.
     */
    public static function assertValid(string $id): void
    {
        if ($id === '') {
            $message   = 'ID_REQUIRED';
            $exception = new DomainValidationException($message);
                    
            throw $exception;
        }

        if (UuidGenerator::isValid($id) === false) {
            $message   = 'ID_INVALID';
            $exception = new DomainValidationException($message);
            
            throw $exception;
        }
    }
}