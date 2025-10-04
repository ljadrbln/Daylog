import type {HttpClient} from '@src/Infrastructure/Http/HttpClient';
import type {Entry} from '@src/Domain/Entries/Entry';
import type {UseCaseResponse} from '@src/Application/DTO/Common/UseCaseResponse';

/**
 * UC-3: Get Entry (Frontend)
 *
 * Fetches a single entry by ID from /api/entries/:id.
 * Performs minimal transport-level validation:
 * - res.ok must be true;
 * - success must be true;
 * - data.item must be a non-null object.
 *
 * Does not validate Entry fields (handled in higher layers).
 */
export class GetEntryGateway {
    private readonly http: HttpClient;

    constructor(http: HttpClient) {
        this.http = http;
    }

    async get(id: string): Promise<Entry> {
        const url = `/api/entries/${id}`;
        const json = await this.http.request<UseCaseResponse<{item: Entry}>>('GET', url);

        const ok = json?.success === true;
        const hasItem = typeof json?.data?.item === 'object' && json.data.item !== null;

        if (!ok || !hasItem) {
            const message = 'Malformed response for GET /api/entries/:id';
            throw new Error(message);
        }

        const entry = json.data!.item;
        return entry;
    }
}
