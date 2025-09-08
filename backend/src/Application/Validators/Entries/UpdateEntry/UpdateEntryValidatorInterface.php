<?php
declare(strict_types=1);

namespace Daylog\Application\Validators\Entries\UpdateEntry;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;

/**
 * Validates UpdateEntry request against business rules.
 */
interface UpdateEntryValidatorInterface
{
    /**
     * @param UpdateEntryRequestInterface $request
     * @return void
     *
     * @throws DomainValidationException On any BR-violation (TITLE_*, BODY_*, DATE_*).
     */
    public function validate(UpdateEntryRequestInterface $request): void;
}
