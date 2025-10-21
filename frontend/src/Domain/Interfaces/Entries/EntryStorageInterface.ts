/* eslint-disable no-unused-vars */

/**
 * EntryStorageInterface
 *
 * Purpose:
 * Low-level persistence adapter to be used by the repository. Mirrors backend storage contract.
 *
 * Mechanics:
 * - `save` performs upsert based on entry id existence.
 * - `findByCriteria` returns plain object with pagination meta.
 */
import type {Entry} from '@src/Domain/Models/Entries/Entry';
import type {ListEntriesCriteriaInterface} from '@src/Domain/Interfaces/Entries/ListEntriesCriteriaInterface';
import type {ListEntriesPageInterface} from '@src/Domain/Interfaces/Entries/ListEntriesPageInterface';

export interface EntryStorageInterface {
    /**
     * Persist an entry into the storage layer.
     *
     * @param {Entry} entry Domain entry.
     * @returns {Promise<void>} Nothing on completion.
     */
    save(entry: Entry): Promise<void>;

    /**
     * Find entry by id.
     *
     * @param {string} id Entry identifier (UUID).
     * @returns {Promise<Entry|null>} Entry or null if not found.
     */
    findById(id: string): Promise<Entry | null>;

    /**
     * Delete an entry by id.
     *
     * @param {string} id Entry identifier (UUID).
     * @returns {Promise<void>} Nothing on completion.
     */
    deleteById(id: string): Promise<void>;

    /**
     * Fetch a page of entries by criteria (UC-2).
     *
     * @param {ListEntriesCriteriaInterface} criteria Normalized criteria.
     * @returns {Promise<ListEntriesPageInterface>} Plain object with page metadata.
     */
    findByCriteria(criteria: ListEntriesCriteriaInterface): Promise<ListEntriesPageInterface>;
}
