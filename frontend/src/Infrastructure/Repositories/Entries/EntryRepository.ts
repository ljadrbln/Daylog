import type {HttpClient, RequestOptions} from '@src/Infrastructure/Http/HttpClient';
import type {Entry} from '@src/Domain/Models/Entries/Entry';
import type {EntryRepositoryInterface} from '@src/Domain/Interfaces/Entries/EntryRepositoryInterface';
import type {ListEntriesCriteriaInterface} from '@src/Domain/Interfaces/Entries/ListEntriesCriteriaInterface';
import type {ListEntriesPageInterface} from '@src/Domain/Interfaces/Entries/ListEntriesPageInterface';
import type {UseCaseResponse} from '@src/Application/DTO/Common/UseCaseResponse';
import {ResponseValidator} from '@src/Infrastructure/Http/ResponseValidator';
import {Endpoints} from '@src/Infrastructure/Http/Endpoints';

/**
 * EntryRepository (frontend, HTTP-backed).
 *
 * Purpose:
 * Implements EntryRepositoryInterface using HttpClient and mirrors backend semantics:
 * - Queries may return `null` for 404 (resource absence is not exceptional).
 * - Commands never return `null`; business/validation errors are thrown as exceptions.
 *
 * Mechanics:
 * - Validates transport envelope via ResponseValidator before returning domain data.
 */
export class EntryRepository implements EntryRepositoryInterface {
    private readonly http: HttpClient;

    /**
     * @param {HttpClient} http Low-level HTTP client.
     */
    public constructor(http: HttpClient) {
        this.http = http;
    }

    /**
     * Get a single Entry by id (UC-3).
     *
     * Purpose:
     * Retrieve a single Entry by id.
     * Repository never maps 404 to `null` — errors bubble up to Presentation.
     *
     * Mechanics:
     * - Performs GET /api/entries/{id}.
     * - ResponseValidator.extractData():
     *   - success:true  → returns Entry.
     *   - any 4xx/5xx   → throws HttpError (status, code).
     *
     * @param {string} id Entry identifier (UUID v4).
     * @returns {Promise<Entry>} Retrieved Entry.
     * @throws {HttpError} For 404 or other non-2xx transport errors.
     */
    public async findById(id: string): Promise<Entry> {
        const url = Endpoints.entryById(id);
        const json = await this.http.request<UseCaseResponse<Entry>>('GET', url);

        const entry = ResponseValidator.extractData<Entry>(json, 'GET /api/entries/:id');
        return entry;
    }

    /**
     * List entries with optional filters and pagination (UC-2).
     *
     * Purpose:
     * Build GET /api/entries with query params from criteria, validate envelope,
     * and map transport DTO into domain page object.
     *
     * @param {ListEntriesCriteriaInterface} criteria Normalized/validated list params.
     * @returns {Promise<ListEntriesPageInterface>} Page with items and pagination meta.
     * @throws {Error} When backend returns null or invalid payload.
     */
    public async list(criteria: ListEntriesCriteriaInterface): Promise<ListEntriesPageInterface> {
        const params = new URLSearchParams();

        if (typeof criteria.page === 'number') {
            params.set('page', String(criteria.page));
        }

        if (typeof criteria.perPage === 'number') {
            params.set('perPage', String(criteria.perPage));
        }

        if (criteria.query) {
            params.set('query', criteria.query);
        }

        if (criteria.dateFrom) {
            params.set('dateFrom', criteria.dateFrom);
        }

        if (criteria.dateTo) {
            params.set('dateTo', criteria.dateTo);
        }

        if (criteria.sortField) {
            params.set('sortField', criteria.sortField);
        }

        if (criteria.sortDir) {
            params.set('sortDir', criteria.sortDir);
        }

        const baseUrl = Endpoints.entries();
        const query = params.toString();
        const url = `${baseUrl}?${query}`;

        const json = await this.http.request<UseCaseResponse<ListEntriesPageInterface>>('GET', url);
        const data = ResponseValidator.extractData<ListEntriesPageInterface>(
            json,
            'GET /api/entries'
        );

        if (data === null) {
            const message = 'Unexpected null payload for GET /api/entries';
            throw new Error(message);
        }

        const page: ListEntriesPageInterface = {
            items: data.items,
            page: data.page,
            perPage: data.perPage,
            total: data.total,
            pagesCount: data.pagesCount
        };

        return page;
    }

    /**
     * Save Entry (backend-aligned upsert).
     *
     * Mechanics:
     * - id === ''  → POST /api/entries  (create).
     * - id !== ''  → PUT /api/entries/{id} (update).
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
     * Delete an Entry by its identifier (UC-4).
     *
     * Purpose:
     * Executes deletion of the specified Entry and returns the deleted Entry on success.
     *
     * Mechanics:
     * - DELETE /api/entries/{id}
     * - Success: returns deleted Entry (never null)
     * - Not found: ResponseValidator.extractData() returns null → convert to 404 error
     *
     * @param {string} id Entry identifier (UUID)
     * @returns {Promise<Entry>} Deleted Entry object
     * @throws {Error} When backend returns malformed payload or non-2xx transport error
     */
    public async deleteById(id: string): Promise<Entry> {
        const url = Endpoints.entryById(id);
        const json = await this.http.request<UseCaseResponse<Entry>>('DELETE', url);

        const data = ResponseValidator.extractData<Entry>(json, 'DELETE /api/entries/:id');

        if (data === null) {
            this.throwNotFound();
        }

        return data;
    }

    /**
     * POST /api/entries (create).
     *
     * Purpose:
     * Send a new entry to the backend and return the persisted entity.
     * Guarantees non-null response; throws if backend payload is malformed.
     *
     * @param {Entry} entry Entry data to persist (title, body, date).
     * @returns {Promise<Entry>} Persisted entry from backend.
     * @throws {Error} When backend returns null or invalid payload.
     */
    private async create(entry: Entry): Promise<Entry> {
        const url = Endpoints.entries();
        const options: RequestOptions = {requestBody: entry};

        const json = await this.http.request<UseCaseResponse<Entry>>('POST', url, options);
        const data = ResponseValidator.extractData<Entry>(json, 'POST /api/entries');

        if (data === null) {
            const message = 'Unexpected null payload for POST /api/entries';
            throw new Error(message);
        }

        return data;
    }

    /**
     * PUT /api/entries/{id} (update).
     *
     * Purpose:
     * Update an existing entry on the backend and return the persisted entity.
     * Commands never return null. Not found must be thrown as 404 error.
     *
     * @param {Entry} entry Entry data to update (must include valid id).
     * @returns {Promise<Entry>} Updated entry from backend.
     * @throws {Error} When backend returns null or invalid payload.
     */
    private async update(entry: Entry): Promise<Entry> {
        const url = Endpoints.entryById(entry.id);
        const options: RequestOptions = {requestBody: entry};

        const json = await this.http.request<UseCaseResponse<Entry>>('PUT', url, options);
        const data = ResponseValidator.extractData<Entry>(json, 'PUT /api/entries/:id');

        if (data === null) {
            this.throwNotFound();
        }

        return data;
    }

    /**
     * Throw standardized 404 error for ENTRY_NOT_FOUND.
     *
     * @throws {Error} Error object with { status:404, code:'ENTRY_NOT_FOUND' }
     */
    private throwNotFound(): never {
        const message = 'ENTRY_NOT_FOUND';
        const error = new Error(message) as Error & {status?: number; code?: string};

        error.status = 404;
        error.code = message;

        throw error;
    }
}
