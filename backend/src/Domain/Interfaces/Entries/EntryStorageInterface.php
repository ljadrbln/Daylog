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
     * @return void
     */
    public function insert(Entry $entry): void;

    /**
     * Retrieve a single Entry by its id (UC-3 GetEntry).
     *
     * Storage responsibilities:
     * - Lookup by primary key.
     * - Return null if not found.
     *
     * @param string $id UUIDv4 identifier of the Entry.
     * @return Entry|null Domain Entry if found, otherwise null.
     */
    public function findById(string $id): ?Entry;
    
    /**
     * Delete entry by UUID.
     *
     * Purpose:
     * Provide low-level deletion of an entry record by its UUID.
     * This method must silently do nothing if the given UUID does not exist in storage.
     *
     * Mechanics:
     * - Accept a UUID string as identifier.
     * - Perform a delete operation in underlying storage.
     * - Return no value (void).
     *
     * @param string $id UUID of the entry to delete.
     * @return void
     */
    public function deleteById(string $id): void;
    
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
