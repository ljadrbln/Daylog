<?php
declare(strict_types=1);

namespace Daylog\Application\Validators\Entries;

use Daylog\Application\DTO\Entries\ListEntriesRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;

/**
 * Validates ListEntries request against business rules.
 *
 * @template T of ListEntriesRequestInterface
 */
interface ListEntriesValidatorInterface
{
    /**
     * @param ListEntriesRequestInterface $request
     * @return void
     *
     * @throws DomainValidationException On any BR-violation (DATE_INVALID, DATE_RANGE_INVALID, PAGE_INVALID, PER_PAGE_INVALID, etc).
     */
    public function validate(ListEntriesRequestInterface $request): void;
}
