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
     * @param Entry $entry Pre-built domain Entry (id/timestamps already set).
     * @return Entry The same Entry instance after persistence.
     */
    public function save(Entry $entry): Entry;

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
     * Retrieve an entry by its identifier.
     *
     * @param string $id UUID identifier of the entry.
     * @return Entry|null Entry if found, otherwise null.
     */
    public function findById(string $id): ?Entry;    
}
