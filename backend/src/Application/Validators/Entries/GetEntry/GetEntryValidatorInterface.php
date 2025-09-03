<?php
declare(strict_types=1);

namespace Daylog\Application\Validators\Entries\GetEntry;

use Daylog\Application\DTO\Entries\GetEntry\GetEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;

/**
 * Validates GetEntry request against business rules.
 *
 * @template T of GetEntryValidatorInterface
 */
interface GetEntryValidatorInterface
{
    /**
     * @param GetEntryRequestInterface $request
     * @return void
     *
     * @throws DomainValidationException If id isn't correct.
     */
    public function validate(GetEntryRequestInterface $request): void;
}
