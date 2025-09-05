<?php
declare(strict_types=1);

namespace Daylog\Application\Validators\Entries\DeleteEntry;

use Daylog\Application\DTO\Entries\DeleteEntry\DeleteEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;

/**
 * Validates DeleteEntry request against UC-4 constraints.
 *
 * Purpose:
 * Ensure the UUID format and any extra UC-level constraints before delete.
 *
 * Mechanics:
 * - Throws DomainValidationException on any violation (e.g., ID_INVALID).
 */
interface DeleteEntryValidatorInterface
{
    /**
     * Validate the request.
     *
     * @param DeleteEntryRequestInterface $request
     * @return void
     *
     * @throws DomainValidationException On ID format errors or other UC-level violations.
     */
    public function validate(DeleteEntryRequestInterface $request): void;
}
