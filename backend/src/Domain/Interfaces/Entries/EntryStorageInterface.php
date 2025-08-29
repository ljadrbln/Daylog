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
     * Insert the given Entry into the database.
     *
     * Design:
     * - Storage DOES NOT generate id/timestamps.
     * - Return value indicates success of the INSERT.
     *
     * @param Entry $entry Domain Entry ready to persist.
     * @return bool True if exactly one row was inserted; false otherwise.
     */
    public function insert(Entry $entry): void;

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
