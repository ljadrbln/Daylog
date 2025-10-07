import type {HttpClient} from '@src/Infrastructure/Http/HttpClient';
import type {Entry} from '@src/Domain/Entries/Entry';
import type {UseCaseResponse} from '@src/Application/DTO/Common/UseCaseResponse';

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
     * Sends entry payload to POST /api/entries and returns created Entry.
     *
     * @param {{title:string; body:string; date:string}} req Valid AddEntry request payload.
     * @returns {Promise<Entry>} Created entry returned by the server.
     * @throws {Error} If transport or response structure is invalid.
     */
    async add(req: {title: string; body: string; date: string}): Promise<Entry> {
        const url = '/api/entries';
        const json = await this.http.request<UseCaseResponse<{item: Entry}>>('POST', url, req);

        const ok = json?.success === true;
        const hasItem = typeof json?.data?.item === 'object' && json.data.item !== null;

        if (!ok || !hasItem) {
            const message = 'Malformed response for POST /api/entries';
            throw new Error(message);
        }

        const entry = json.data!.item;
        return entry;
    }
}
