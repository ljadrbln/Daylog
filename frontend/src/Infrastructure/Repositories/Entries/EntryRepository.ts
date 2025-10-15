import type {HttpClient} from '@src/Infrastructure/Http/HttpClient';
import type {Entry} from '@src/Domain/Entries/Entry';
import type {EntryRepositoryInterface} from '@src/Domain/Interfaces/Entries/EntryRepositoryInterface';
import type {ListEntriesCriteriaInterface} from '@src/Domain/Interfaces/Entries/ListEntriesCriteriaInterface';
import type {ListEntriesPageInterface} from '@src/Domain/Interfaces/Entries/ListEntriesPageInterface';
import type {UseCaseResponse} from '@src/Application/DTO/Common/UseCaseResponse';
import {ResponseValidator} from '@src/Infrastructure/Http/ResponseValidator';

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
        const url = `/api/entries/${id}`;
        const json = await this.http.request<UseCaseResponse<Entry>>('GET', url);

        const entry = ResponseValidator.extractData(json, 'GET /api/entries/:id');

        return entry;
    }

    // stubs for other UC — will be filled later
    public async save(entry: Entry): Promise<Entry> {
        throw new Error('Not implemented yet');
    }

    /**
     * Delete an entry by its identifier (UC-4).
     *
     * Mechanics:
     * - Perform DELETE /api/entries/{id}.
     * - Validate transport envelope (success=true). Payload may contain Entry, but is ignored here.
     *
     * @param {string} id Entry identifier (UUID).
     * @returns {Promise<Entry>} Resolves on success.
     * @throws {Error} If transport envelope is malformed or HTTP non-2xx raised upstream.
     */
    public async deleteById(id: string): Promise<Entry> {
        const url = `/api/entries/${id}`;
        const json = await this.http.request<UseCaseResponse<Entry>>('DELETE', url);

        const entry = ResponseValidator.extractData(json, 'DELETE /api/entries/:id');

        return entry;
    }

    public async findByCriteria(
        criteria: ListEntriesCriteriaInterface
    ): Promise<ListEntriesPageInterface> {
        throw new Error('Not implemented yet');
    }
}
