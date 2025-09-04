<?php
declare(strict_types=1);

namespace Daylog\Application\Interfaces;

/**
 * Common contract for validation exceptions.
 * Provides error code/message and category identifier.
 */
interface InputValidationExceptionInterface extends \Throwable
{
    /**
     * @return string The single error code/message.
     */
    public function getError(): string;

    /**
     * @return string Category of the validation error: 'TRANSPORT' | 'DOMAIN'
     */
    public function getCategory(): string;
}