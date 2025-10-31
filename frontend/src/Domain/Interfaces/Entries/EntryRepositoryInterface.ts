/* eslint-disable no-unused-vars */

import type {Entry} from '@src/Domain/Models/Entries/Entry';
import type {ListEntriesCriteriaInterface} from '@src/Domain/Interfaces/Entries/ListEntriesCriteriaInterface';
import type {ListEntriesPageInterface} from '@src/Domain/Interfaces/Entries/ListEntriesPageInterface';

/**
 * Frontend Entry repository contract (UC-2/3/4/5).
 *
 * Purpose:
 * Express query/command semantics at the domain boundary.
 *
 * Mechanics:
 * - Queries may return null (e.g., findById on 404).
 * - Commands must throw on non-executable operations (e.g., delete/update on 404).
 */
export interface EntryRepositoryInterface {
    /**
     * Load a single entry by identifier (UC-3).
     *
     * @param id string UUID v4
     * @returns Promise<Entry|null> Null on HTTP Error
     */
    findById(id: string): Promise<Entry | null>;

    /**
     * Delete an entry by identifier (UC-4).
     *
     * @param id string UUID v4
     * @returns Promise<Entry|null> Deleted entry object from `data`
     */
    deleteById(id: string): Promise<Entry | null>;

    /**
     * Save (create or update) an entry (UC-1/5).
     *
     * @param entry Entry
     * @returns Promise<Entry> Persisted Entry object from `data`
     */
    save(entry: Entry): Promise<Entry>;

    /**
     * List entries (UC-2).
     *
     * Purpose:
     * Retrieve paginated list of entries with optional filters.
     *
     * @param {ListEntriesCriteriaInterface} criteria Normalized/validated filters.
     * @returns {Promise<ListEntriesPageInterface>} Page of entries with pagination meta.
     */
    list(criteria: ListEntriesCriteriaInterface): Promise<ListEntriesPageInterface>;
}
