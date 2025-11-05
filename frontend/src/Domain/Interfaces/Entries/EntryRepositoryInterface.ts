/* eslint-disable no-unused-vars */

import type {Entry} from '@src/Domain/Models/Entries/Entry';
import type {ListEntriesCriteriaInterface} from '@src/Domain/Interfaces/Entries/ListEntriesCriteriaInterface';
import type {ListEntriesPageInterface} from '@src/Domain/Interfaces/Entries/ListEntriesPageInterface';

/**
 * EntryRepositoryInterface
 *
 * Purpose:
 * Domain-level port for Entries. Keeps HTTP/transport details out of the domain.
 *
 * Semantics:
 * - Repository never returns `null`; absence or transport errors are exceptional.
 * - All 4xx/5xx responses are surfaced as typed errors:
 *   - NotFoundError  (status:404, code:'ENTRY_NOT_FOUND') for missing resources.
 *   - DomainValidationError (status:422, code:'...') for business rule violations.
 *
 * This interface mirrors backend contracts but delegates all error handling
 * to upper layers (Application/Presentation).
 */

export interface EntryRepositoryInterface {
    /**
     * Retrieve a single entry by id (UC-3 GetEntry).
     *
     * Error semantics:
     * - Does not return null.
     * - Propagates transport/domain errors upward (e.g., NotFoundError with {status:404, code:'ENTRY_NOT_FOUND'}).
     *
     * @param {string} id Entry identifier (UUID v4).
     * @returns {Promise<Entry>} Resolved Entry from backend.
     */
    findById(id: string): Promise<Entry>;

    /**
     * List entries with optional filters and pagination (UC-2 ListEntries).
     *
     * @param {ListEntriesCriteriaInterface} criteria Normalized list parameters.
     * @returns {Promise<ListEntriesPageInterface>} Page object with items and meta.
     */
    list(criteria: ListEntriesCriteriaInterface): Promise<ListEntriesPageInterface>;

    /**
     * Create or update an entry (UC-1 AddEntry / UC-5 UpdateEntry).
     *
     * Rules:
     * - Create when entry.id === ''.
     * - Update when entry.id !== ''.
     *
     * Error semantics:
     * - Propagates NotFoundError {status:404, code:'ENTRY_NOT_FOUND'} on updating a missing entry.
     * - Propagates DomainValidationError {status:422, code:'...'} on business rule violations.
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
     *
     * Error semantics:
     * - Does not return null.
     * - Propagates NotFoundError {status:404, code:'ENTRY_NOT_FOUND'} if entry does not exist.
     *
     * @param {string} id Entry identifier (UUID v4).
     * @returns {Promise<Entry>} Deleted Entry returned by backend (200 OK).
     */
    deleteById(id: string): Promise<Entry>;
}
