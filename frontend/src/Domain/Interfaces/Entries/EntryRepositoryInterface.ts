/* eslint-disable no-unused-vars */

import type {Entry} from '@src/Domain/Models/Entries/Entry';
import type {ListEntriesCriteriaInterface} from '@src/Domain/Interfaces/Entries/ListEntriesCriteriaInterface';
import type {ListEntriesPageInterface} from '@src/Domain/Interfaces/Entries/ListEntriesPageInterface';

/**
 * EntryRepositoryInterface
 *
 * Purpose:
 * Domain-level port for Entries. Keeps HTTP/transport details out of the domain.
 * Mirrors backend semantics exactly:
 * - Queries may return `null` for 404 (resource absence is not exceptional).
 * - Commands never return `null`; business/validation errors are thrown as exceptions:
 *   - NotFoundError (status: 404, code: 'ENTRY_NOT_FOUND') for Update/Delete when id doesn't exist.
 *   - DomainValidationError (status: 422, code: '...') for title/body/date rule violations.
 */
export interface EntryRepositoryInterface {
    /**
     * Retrieve a single entry by id (UC-3 GetEntry).
     *
     * @param {string} id Entry identifier (UUID).
     * @returns {Promise<Entry|null>} Entry or null when not found (404).
     */
    findById(id: string): Promise<Entry | null>;

    /**
     * List entries with optional filters and pagination (UC-2 ListEntries).
     *
     * @param {ListEntriesCriteriaInterface} criteria Normalized list params.
     * @returns {Promise<ListEntriesPageInterface>} Page object with items & meta.
     */
    list(criteria: ListEntriesCriteriaInterface): Promise<ListEntriesPageInterface>;

    /**
     * Create or update an entry (UC-1 AddEntry / UC-5 UpdateEntry).
     *
     * Rules:
     * - Create when entry.id === ''.
     * - Update when entry.id !== ''.
     * - Never returns null.
     *
     * Error semantics (mirrors backend):
     * - Throws NotFoundError { status: 404, code: 'ENTRY_NOT_FOUND' } when updating a missing entry.
     * - Throws DomainValidationError { status: 422, code: '...' } on BR violations.
     *
     * @param {Entry} entry Entry to persist.
     * @returns {Promise<Entry>} Persisted Entry from backend.
     */
    save(entry: Entry): Promise<Entry>;

    /**
     * Delete an entry by id (UC-4 DeleteEntry).
     *
     * Mechanics:
     * - Performs DELETE /api/entries/{id}.
     * - Returns deleted Entry object on success (200 OK).
     * - Throws NotFoundError { status: 404, code: 'ENTRY_NOT_FOUND' } if entry does not exist.
     *
     * @param {string} id Entry identifier (UUID).
     * @returns {Promise<Entry>} Deleted Entry object from backend.
     */
    deleteById(id: string): Promise<Entry>;
}
