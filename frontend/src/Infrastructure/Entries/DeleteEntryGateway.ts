import type {HttpClient} from '@src/Infrastructure/Http/HttpClient';
import type {Entry} from '@src/Domain/Entries/Entry';
import type {DeleteEntryRequest} from '@src/Application/DTO/Entries/DeleteEntry/DeleteEntryRequest';
import type {UseCaseResponse} from '@src/Application/DTO/Common/UseCaseResponse';

/**
 * UC-4: Delete Entry (Frontend)
 *
 * Sends DELETE /api/entries/:id and validates transport envelope:
 * - success must be true;
 * - data must be a non-null object.
 * Otherwise throws an Error with a descriptive message.
 */
export class DeleteEntryGateway {
    private readonly http: HttpClient;

    constructor(http: HttpClient) {
        this.http = http;
    }

    async delete(req: DeleteEntryRequest): Promise<Entry> {
        const url = `/api/entries/${req.id}`;
        const json = await this.http.request<UseCaseResponse<Entry>>('DELETE', url);

        // success flag must be true
        if (json.success !== true) {
            const message = 'Malformed response for DELETE /api/entries/:id';
            throw new Error(message);
        }

        // data must be a non-null object
        const hasData = typeof json.data === 'object' && json.data !== null;
        if (!hasData) {
            const message = 'Malformed response for DELETE /api/entries/:id';
            throw new Error(message);
        }

        const entry = json.data as Entry;
        return entry;
    }
}
