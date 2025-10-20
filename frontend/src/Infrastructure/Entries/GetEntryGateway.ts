import type {HttpClient} from '@src/Infrastructure/Http/HttpClient';
import type {Entry} from '@src/Domain/Models/Entries/Entry';
import type {UseCaseResponse} from '@src/Application/DTO/Common/UseCaseResponse';
import type {GetEntryRequest} from '@src/Application/DTO/Entries/GetEntry/GetEntryRequest';

/**
 * UC-3: Get Entry (Frontend)
 *
 * Fetches a single entry by ID from GET /api/entries/:id.
 *
 * Transport-level validation:
 * - HTTP must be ok (delegated to HttpClient);
 * - response.success must be true;
 * - response.data must be a non-null object (Entry).
 *
 * Field-level validation is out of scope here (handled by backend/higher layers).
 */
export class GetEntryGateway {
    private readonly http: HttpClient;

    constructor(http: HttpClient) {
        this.http = http;
    }

    /**
     * UC-3: Get Entry (Gateway)
     *
     * Performs GET /api/entries/{id} and returns the Entry from response.data.
     *
     * @param {GetEntryRequest} req Path parameter with entry ID.
     * @returns {Promise<Entry>} Resolved entry object from backend.
     * @throws {Error} If HTTP is non-2xx or the transport envelope is malformed.
     */
    async get(req: GetEntryRequest): Promise<Entry> {
        const url = `/api/entries/${req.id}`;
        const json = await this.http.request<UseCaseResponse<Entry>>('GET', url);

        if (json.success !== true) {
            const message = 'Malformed response for GET /api/entries/:id';
            throw new Error(message);
        }

        const hasData = typeof json.data === 'object' && json.data !== null;
        if (!hasData) {
            const message = 'Malformed response for GET /api/entries/:id';
            throw new Error(message);
        }

        const entry = json.data as Entry;

        return entry;
    }
}
