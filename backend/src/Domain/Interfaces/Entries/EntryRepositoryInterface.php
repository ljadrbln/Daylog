<?php

declare(strict_types=1);

namespace Daylog\Domain\Interfaces\Entries;

use Daylog\Domain\Models\Entries\Entry;
use Daylog\Domain\Models\Entries\ListEntriesCriteria;

/**
 * Repository contract for Entries.
 *
 * Purpose:
 * Define read operations to retrieve entries. For UC-2 the primary
 * method is criteria-based pagination, where sorting/filtering/paging
 * are applied in the infrastructure implementation.
 */
interface EntryRepositoryInterface
{
    /**
     * Save a new Entry into storage.
     *
     * @param Entry $entry
     * @return array<string,string> Result with keys:
     *                             - id (UUID v4)
     *                             - title (string)
     *                             - body (string)
     *                             - date (YYYY-MM-DD)
     *                             - createdAt (ISO-8601 UTC)
     *                             - updatedAt (ISO-8601 UTC)
     */
    public function save(Entry $entry): array;

    /**
     * Fetch a paginated page of entries by domain criteria.
     *
     * Mechanics:
     * - Apply filters from ListEntriesCriteria (date/dateFrom/dateTo/query).
     * - Apply sorting (primary from criteria + stable secondary createdAt DESC).
     * - Apply pagination (page/perPage).
     *
     * @param ListEntriesCriteria $criteria Domain query object.
     * @return array{
     *     items:      list<Entry>,
     *     total:      int,
     *     page:       int,
     *     perPage:    int,
     *     pagesCount: int
     * }
     */
    public function findByCriteria(ListEntriesCriteria $criteria): array;

    /**
     * Return all entries for read-side use cases.
     *
     * The write-side (save) stays unchanged; this method exists to let read use cases
     * (e.g., ListEntries) obtain a snapshot of entries to filter/sort/paginate in application layer.
     *
     * @return array<int, array<string, string>> List of normalized entry rows with keys:
     *                                           id, title, body, date, createdAt, updatedAt.
     */
    public function fetchAll(): array;

}
