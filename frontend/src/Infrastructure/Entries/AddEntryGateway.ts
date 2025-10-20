import type {HttpClient} from '@src/Infrastructure/Http/HttpClient';
import type {Entry} from '@src/Domain/Models/Entries/Entry';
import type {UseCaseResponse} from '@src/Application/DTO/Common/UseCaseResponse';
import type {AddEntryRequest} from '@src/Application/DTO/Entries/AddEntry/AddEntryRequest';

/**
 * UC-1: Add Entry (Frontend)
 *
 * Sends a new journal entry to POST /api/entries.
 *
 * Performs minimal transport-level validation:
 * - res.ok must be true;
 * - success must be true;
 * - data.item must be a non-null object.
 *
 * Does not validate Entry fields — handled on the backend and in higher layers.
 */
export class AddEntryGateway {
    private readonly http: HttpClient;

    constructor(http: HttpClient) {
        this.http = http;
    }

    /**
     * UC-1: Add Entry (Frontend, Gateway)
     *
     * Sends AddEntry payload to POST /api/entries and returns the created Entry.
     *
     * Transport-level validation only:
     * - success must be true;
     * - data must be a non-null object (backend returns Entry directly in data).
     *
     * @param {AddEntryRequest} req Valid AddEntry request payload.
     * @returns {Promise<Entry>} Created entry returned by the server.
     * @throws {Error} If HTTP status is non-2xx or response structure is invalid.
     */
    async add(req: AddEntryRequest): Promise<Entry> {
        const url = '/api/entries';
        const json = await this.http.request<UseCaseResponse<Entry>>('POST', url, req);

        if (json.success !== true) {
            const message = 'Malformed response for POST /api/entries';
            throw new Error(message);
        }

        const hasData = typeof json.data === 'object' && json.data !== null;
        if (!hasData) {
            const message = 'Malformed response for POST /api/entries';
            throw new Error(message);
        }

        const entry = json.data as Entry;

        return entry;
    }
}
