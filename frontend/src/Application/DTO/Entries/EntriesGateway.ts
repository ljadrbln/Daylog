import type {Entry} from '../../Domain/Entries/Entry';

/**
 * EntriesGateway defines the application-level contract for loading entries.
 *
 * Intent:
 * - The application uses this interface to obtain entries regardless of the underlying transport (HTTP, IndexedDB, etc.).
 * - Concrete adapters (e.g., HttpEntriesGateway) live in Infrastructure and must implement this contract.
 *
 * Notes:
 * - Kept minimal on purpose: returns a plain array of entries to match the current happy-path slice and tests.
 * - Pagination/sorting/request DTO can be added later when the UI requires it (e.g., list(request: ListEntriesRequest): Promise<ListEntriesData>).
 *
 * @interface EntriesGateway
 * @method list
 * @description Returns a list of entries from a data source.
 * @returns {Promise<Entry[]>} A promise resolved with entries in unspecified order (slice 1).
 */
export interface EntriesGateway {
    list(): Promise<Entry[]>;
}
