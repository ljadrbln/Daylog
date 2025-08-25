<?php
declare(strict_types=1);

namespace Daylog\Domain\Interfaces\Entries;

use Daylog\Domain\Models\Entries\Entry;
use Daylog\Domain\Models\Entries\ListEntriesCriteria;

/**
 * Interface EntryStorageInterface
 *
 * Defines a low-level persistence contract for Entry.
 * Implementations are database-specific.
 */
interface EntryStorageInterface
{
    /**
     * Insert the given Entry and return generated UUID.
     *
     * @param Entry $entry Entry to be persisted.
     * @param string $now   Current timestamp (UTC, ISO 8601).
     * @return string Generated UUID for the new record.
     */
    public function insert(Entry $entry, string $now): string;

    /**
     * Retrieve paginated entries by criteria (UC-2).
     *
     * Storage is responsible for:
     * - applying filters (date/dateFrom/dateTo/query),
     * - applying sort with a stable secondary key (createdAt DESC when primary keys equal),
     * - computing LIMIT/OFFSET,
     * - returning total count for the same filters (without pagination),
     * - shaping a deterministic, stable order per AC-5/AC-8 of UC-2.
     *
     * @param ListEntriesCriteria $criteria
     * @return array{
     *     items: array<int, array{id:string,date:string,title:string,body:string,createdAt:string,updatedAt:string}>,
     *     total: int,
     *     page: int,
     *     perPage: int,
     *     pagesCount: int
     * }
     */
    public function findByCriteria(ListEntriesCriteria $criteria): array;

}
