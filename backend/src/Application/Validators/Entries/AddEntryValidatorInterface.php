<?php
declare(strict_types=1);

namespace Daylog\Application\Validators\Entries;

use Daylog\Application\DTO\Entries\AddEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;

/**
 * Validates AddEntry request against business rules.
 *
 * @template T of AddEntryRequestInterface
 */
interface AddEntryValidatorInterface
{
    /**
     * @param AddEntryRequestInterface $request
     * @return void
     *
     * @throws DomainValidationException On any BR-violation (TITLE_*, BODY_*, DATE_*).
     */
    public function validate(AddEntryRequestInterface $request): void;
}
