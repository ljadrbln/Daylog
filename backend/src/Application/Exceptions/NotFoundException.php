<?php
declare(strict_types=1);

namespace Daylog\Application\Exceptions;

use RuntimeException;
use Daylog\Application\Interfaces\InputValidationExceptionInterface;

/**
 * NotFoundException
 *
 * Purpose:
 * Signals that a domain entity requested by a use case does not exist.
 * This is a domain-level outcome mapped by Presentation to HTTP 404 with code ENTRY_NOT_FOUND.
 *
 * Mechanics:
 * - Carries a single error code/message string (e.g., 'ENTRY_NOT_FOUND').
 * - Implements InputValidationExceptionInterface for unified handling in Presentation.
 *
 */
final class NotFoundException extends RuntimeException implements InputValidationExceptionInterface
{
    /** @var string */
    private $error;

    /**
     * @param string $error Domain error code (e.g., 'ENTRY_NOT_FOUND').
     */
    public function __construct(string $error)
    {
        $message = $error;
        parent::__construct($message);
        $this->error = $error;
    }

    /**
     * Return the domain error code (stable identifier for clients and tests).
     *
     * @return string
     */
    public function getError(): string
    {
        $error = $this->error;

        return $error;
    }

    /**
     * Category hint for centralized handlers/logging.
     * Domain-level, but mapped to 404 by Presentation.
     *
     * @return string
     */
    public function getCategory(): string
    {
        $category = 'DOMAIN';

        return $category;
    }
}
