/* eslint-disable no-unused-vars */

/**
 * EntryRepositoryInterface
 *
 * Purpose:
 * Define repository contract mirroring the backend style. The repository orchestrates
 * persistence via a storage adapter and exposes CRUD + list operations on the domain model.
 *
 * Notes:
 * - No Application DTOs here; only domain types.
 * - `save` performs upsert semantics.
 */
import type {Entry} from '@src/Domain/Entries/Entry';
import type {ListEntriesCriteriaInterface} from '@src/Domain/Interfaces/Entries/ListEntriesCriteriaInterface';
import type {ListEntriesPageInterface} from '@src/Domain/Interfaces/Entries/ListEntriesPageInterface';

export interface EntryRepositoryInterface {
    /**
     * Persist an entry (create or update).
     *
     * @param {Entry} entry Domain entry to be saved.
     * @returns {Promise<Entry>} The same entry instance (with possible server-adjusted fields).
     */
    save(entry: Entry): Promise<Entry>;

    /**
     * Find entry by its identifier.
     *
     * @param {string} id Entry identifier (UUID).
     * @returns {Promise<Entry|null>} Entry or null when not found.
     */
    findById(id: string): Promise<Entry | null>;

    /**
     * Delete an entry by its identifier.
     *
     * @param {string} id Entry identifier (UUID).
     * @returns {Promise<Entry|null>} Entry or null when not found.
     */
    deleteById(id: string): Promise<Entry | null>;

    /**
     * Fetch a page of entries by criteria (UC-2).
     *
     * @param {ListEntriesCriteriaInterface} criteria Normalized criteria (validated upstream).
     * @returns {Promise<{ListEntriesPageInterface}>} Plain object with page metadata.
     */
    findByCriteria(criteria: ListEntriesCriteriaInterface): Promise<ListEntriesPageInterface>;
}
