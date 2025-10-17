import type {HttpClient, RequestOptions} from '@src/Infrastructure/Http/HttpClient';
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

    /**
     * Save Entry (backend-aligned upsert).
     *
     * Mechanics:
     * - id === ''  → POST /api/entries  with Entry body.
     * - id !== ''  → PATCH /api/entries/{id} with Entry body.
     *
     * @param {Entry} entry Fully prepared domain Entry.
     * @returns {Promise<Entry>} Persisted entry returned by backend.
     */
    public async save(entry: Entry): Promise<Entry> {
        const hasId = entry.id !== '';

        if (!hasId) {
            const created = await this.create(entry);
            return created;
        }

        const updated = await this.update(entry);
        return updated;
    }

    /**
     * POST /api/entries.
     *
     * Purpose:
     * - Delegate UC-1 create logic to backend.
     *
     * @param {Entry} entry Ready entry data (title, body, date).
     * @returns {Promise<Entry>} Persisted entry.
     */
    private async create(entry: Entry): Promise<Entry> {
        const method = 'POST';
        const url = '/api/entries';
        const endpoint = `${method} ${url}`;

        const options: RequestOptions = {requestBody: entry};
        const response = await this.http.request<UseCaseResponse<Entry>>(method, url, options);

        const created = ResponseValidator.extractData(response, endpoint);

        return created;
    }

    /**
     * PUT /api/entries/{id}.
     *
     * Purpose:
     * - Delegate UC-5 update logic to backend.
     * - Backend strips immutable fields internally.
     *
     * @param {Entry} entry Ready entry data with id.
     * @returns {Promise<Entry>} Updated entry.
     */
    private async update(entry: Entry): Promise<Entry> {
        const method = 'PUT';
        const url = `/api/entries/${entry.id}`;
        const endpoint = `${method} /api/entries/:id`;

        const options: RequestOptions = {requestBody: entry};
        const response = await this.http.request<UseCaseResponse<Entry>>(method, url, options);

        const updated = ResponseValidator.extractData(response, endpoint);

        return updated;
    }
}
