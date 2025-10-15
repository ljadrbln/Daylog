import type {HttpClient} from '@src/Infrastructure/Http/HttpClient';
import type {Entry} from '@src/Domain/Entries/Entry';
import type {EntryRepositoryInterface} from '@src/Domain/Interfaces/Entries/EntryRepositoryInterface';
import type {ListEntriesCriteriaInterface} from '@src/Domain/Interfaces/Entries/ListEntriesCriteriaInterface';
import type {ListEntriesPageInterface} from '@src/Domain/Interfaces/Entries/ListEntriesPageInterface';

/**
 * EntryRepository (frontend, HTTP-backed)
 *
 * Purpose:
 * Implements EntryRepositoryInterface using HttpClient.
 * Performs all CRUD and list operations against REST endpoints.
 *
 * - Mirrors the old GetEntryGateway and others under /Infrastructure/Entries/.
 * - Validates transport envelope before returning domain data.
 */
export class EntryRepository implements EntryRepositoryInterface {
    private readonly http: HttpClient;

    constructor(http: HttpClient) {
        this.http = http;
    }

    /**
     * Fetch an entry by id (UC-3).
     *
     * @param {string} id Entry identifier.
     * @returns {Promise<Entry>} Entry object from backend.
     * @throws {Error} if response.success !== true or data malformed.
     */
    public async findById(id: string): Promise<Entry> {
        throw new Error('Not implemented');
    }

    // stubs for other UC — will be filled later
    public async save(entry: Entry): Promise<Entry> {
        throw new Error('Not implemented yet');
    }

    public async deleteById(id: string): Promise<void> {
        throw new Error('Not implemented yet');
    }

    public async findByCriteria(criteria: ListEntriesCriteriaInterface): Promise<ListEntriesPageInterface> {
        throw new Error('Not implemented yet');
    }
}
