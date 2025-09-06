<?php

declare(strict_types=1);

namespace Daylog\Application\UseCases\Entries\ListEntries;

use Daylog\Application\DTO\Entries\ListEntries\ListEntriesRequestInterface;
use Daylog\Application\DTO\Entries\ListEntries\ListEntriesResponseInterface;

/**
 * Use Case Contract: UC-2 ListEntries.
 *
 * Purpose:
 * Define the application-level boundary for listing diary entries.
 * The implementation must validate input according to UC-2 (see BR-1..BR-2 and UC-2 constraints),
 * query the repository/storage, and return a paginated result.
 *
 * Mechanics:
 * - Accepts a DTO request carrying filter/sort/pagination parameters (page, perPage, sort, date, dateFrom/dateTo, query).
 * - Applies normalization and validation (clamping, trimming, date validity).
 * - May throw a DomainValidationException on AF-1 (invalid date), AF-4 (query too long), etc.
 * - On success, retrieves matching entries from storage in deterministic order.
 * - Returns a DTO response with items (Entry models) and pagination metadata.
 *
 * @see docs/use-cases/UC-2-ListEntries.md for parameters, limits, and acceptance criteria.
 */
interface ListEntriesInterface
{
    /**
     * Execute UC-2 ListEntries.
     *
     * @param ListEntriesRequestInterface $request Input DTO with filters, sorting and pagination.
     * @return ListEntriesResponseInterface Response DTO containing entries and pagination metadata.
     */
    public function execute(ListEntriesRequestInterface $request): ListEntriesResponseInterface;
}
