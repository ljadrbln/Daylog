<?php
declare(strict_types=1);

namespace Daylog\Application\Interfaces;

/**
 * Common contract for validation exceptions.
 * Provides flat list of error codes/messages and category identifier.
 */
interface InputValidationExceptionInterface extends \Throwable
{
    /**
     * @return string[] Flat list of error codes/messages (no field keys)
     */
    public function getErrors(): array;

    /**
     * @return string Category of the validation error: 'transport' | 'domain'
     */
    public function getCategory(): string;
}