import type {HttpClient} from '@src/Infrastructure/Http/HttpClient';
import type {Entry} from '@src/Domain/Models/Entries/Entry';
import type {UseCaseResponse} from '@src/Application/DTO/Common/UseCaseResponse';
import type {UpdateEntryRequest} from '@src/Application/DTO/Entries/UpdateEntry/UpdateEntryRequest';

/**
 * UC-5: Update Entry (Frontend)
 *
 * Sends a partial update for an existing entry to PUT /api/entries/:id.
 *
 * Performs minimal transport-level validation:
 * - success must be true;
 * - data must be a non-null object (backend returns Entry directly in data).
 *
 * Does not validate Entry fields — handled on the backend and in higher layers.
 */
export class UpdateEntryGateway {
    private readonly http: HttpClient;

    constructor(http: HttpClient) {
        this.http = http;
    }

    /**
     * UC-5: Update Entry (Frontend, Gateway)
     *
     * Sends UpdateEntry payload to PUT /api/entries/:id and returns the updated Entry.
     *
     * Transport-level validation only:
     * - success must be true;
     * - data must be a non-null object (backend returns Entry directly in data).
     *
     * @param {UpdateEntryRequest} req Valid UpdateEntry request payload.
     * @returns {Promise<Entry>} Updated entry returned by the server.
     * @throws {Error} If HTTP status is non-2xx or response structure is invalid.
     */
    async update(req: UpdateEntryRequest): Promise<Entry> {
        const url = `/api/entries/${req.id}`;
        const json = await this.http.request<UseCaseResponse<Entry>>('PUT', url, req);

        if (json.success !== true) {
            const message = 'Malformed response for PUT /api/entries/:id';

            throw new Error(message);
        }

        const hasData = typeof json.data === 'object' && json.data !== null;
        if (!hasData) {
            const message = 'Malformed response for PUT /api/entries/:id';

            throw new Error(message);
        }

        const entry = json.data as Entry;

        return entry;
    }
}
