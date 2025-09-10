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
     * Save an Entry to persistent storage.
     *
     * Purpose:
     *   Provide a unified entry point for persisting domain entities, regardless of
     *   whether they are new or existing. The caller (Repository/UseCase) does not
     *   need to decide between INSERT or UPDATE operations.
     *
     * Mechanics:
     *   - Check if a row with the given Entry id already exists via the model.
     *   - If it exists, update the row with the new field values.
     *   - If it does not exist, create a new row using the mapped field data.
     *
     * Invariants:
     *   - The id must be a valid UUID v4 (validated earlier in the flow).
     *   - `createdAt` remains unchanged on update; `updatedAt` is refreshed
     *     according to BR-2 (timestamps consistency & monotonicity).
     *
     * @param Entry $entry Domain entity to persist.
     * @return void
     */
    public function save(Entry $entry): void;

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
