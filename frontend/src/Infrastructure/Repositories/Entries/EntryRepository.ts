import type {HttpClient, RequestOptions} from '@src/Infrastructure/Http/HttpClient';
import type {Entry} from '@src/Domain/Models/Entries/Entry';
import type {EntryRepositoryInterface} from '@src/Domain/Interfaces/Entries/EntryRepositoryInterface';
import type {ListEntriesCriteriaInterface} from '@src/Domain/Interfaces/Entries/ListEntriesCriteriaInterface';
import type {ListEntriesPageInterface} from '@src/Domain/Interfaces/Entries/ListEntriesPageInterface';
import type {UseCaseResponse} from '@src/Application/DTO/Common/UseCaseResponse';
import {ResponseValidator} from '@src/Infrastructure/Http/ResponseValidator';
import {Endpoints} from '@src/Infrastructure/Http/Endpoints';
import {extractHttpStatus} from '@src/Infrastructure/Http/utils/extractHttpStatus';

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
     * Get entry by id (UC-3).
     *
     * Purpose:
     * Retrieve a single Entry by id; rely on ResponseValidator for 404→null mapping.
     *
     * Mechanics:
     * - GET /api/entries/{id}
     * - ResponseValidator.extractData() handles:
     *   - success:true → returns Entry
     *   - success:false + 404 → returns null
     *
     * @returns Promise<Entry|null>
     * @throws {Error} When the transport envelope is malformed or a non-2xx HTTP error occurs upstream
     */
    public async findById(id: string): Promise<Entry | null> {
        const url = Endpoints.entryById(id);

        try {
            const json = await this.http.request<UseCaseResponse<Entry>>('GET', url);
            const entry = ResponseValidator.extractData<Entry>(json, 'GET /api/entries/:id');
            return entry;
        } catch (error: unknown) {
            const status = extractHttpStatus(error);

            if (status === 404) {
                return null;
            }

            throw error;
        }
    }

    /**
     * Delete an Entry by its identifier (UC-4).
     *
     * Purpose:
     * Executes deletion of the specified Entry and returns
     * the deleted Entry object received from the API response.
     *
     * Mechanics:
     * - Perform DELETE /api/entries/{id}.
     * - Expect a 200 OK response with transport envelope { success: true, data: Entry }.
     * - Validate the envelope and extract the Entry object from `data`.
     * - Propagate any non-2xx or malformed responses as errors.
     *
     * @param {string} id Entry identifier (UUID)
     * @returns {Promise<Entry | null>} Deleted Entry object containing id, title, body, date, createdAt, updatedAt
     * @throws {Error} When the transport envelope is malformed or a non-2xx HTTP error occurs upstream
     */
    public async deleteById(id: string): Promise<Entry | null> {
        const url = Endpoints.entryById(id);

        try {
            const json = await this.http.request<UseCaseResponse<Entry>>('DELETE', url);
            const entry = ResponseValidator.extractData<Entry>(json, 'DELETE /api/entries/:id');
            return entry;
        } catch (error: unknown) {
            const status = extractHttpStatus(error);

            if (status === 404) {
                return null;
            }

            throw error;
        }
    }

    /**
     * UC-2: List entries with optional filters and pagination.
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
     * - id === ''  → POST /api/entries  with Entry body.
     * - id !== ''  → PUT /api/entries/{id} with Entry body.
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
     * Send a new entry to the backend and return the persisted entity.
     * Guarantees non-null response; throws if backend payload is malformed.
     *
     * Mechanics:
     * - Delegates creation logic to backend (UC-1).
     * - Validates that backend returned a valid Entry object.
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
     * PUT /api/entries/{id}.
     *
     * Purpose:
     * Update an existing entry on the backend and return the persisted entity.
     * Ensures non-null response; throws if backend payload is malformed.
     *
     * Mechanics:
     * - Delegates UC-5 update logic to backend.
     * - Backend strips immutable fields internally (id, timestamps, etc.).
     * - Validates that backend returned a valid updated Entry object.
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
            const message = 'Unexpected null payload for PUT /api/entries/{id}';
            throw new Error(message);
        }

        return data;
    }
}
