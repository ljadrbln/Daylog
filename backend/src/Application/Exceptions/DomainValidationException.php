<?php
declare(strict_types=1);

namespace Daylog\Application\Exceptions;

use RuntimeException;

/**
 * Thrown when DTO passes transport checks but violates domain/business rules.
 * Carries a flat list of error codes/messages (no field keys).
 */
final class DomainValidationException extends RuntimeException
{
    /** @var string[] */
    private array $errors;

    /**
     * @param string[] $errors Flat list of error codes/messages.
     */
    public function __construct(array $errors)
    {
        parent::__construct('Domain validation failed');
        $this->errors = array_values($errors);
    }

    /**
     * @return string[] Flat list of error codes/messages.
     */
    public function getErrors(): array
    {
        $result = $this->errors;
        return $result;
    }
}
