<?php
declare(strict_types=1);

namespace Daylog\Application\Exceptions;

use RuntimeException;

/**
 * Thrown when input fails transport-level validation:
 * missing required fields or wrong types before business rules are checked.
 */
final class TransportValidationException extends RuntimeException
{
    /** @var string[] */
    private array $errors;

    /**
     * @param string[] $errors
     */
    public function __construct(array $errors)
    {
        parent::__construct('Transport validation failed');
        $this->errors = array_values($errors);
    }

    /**
     * @return string[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
