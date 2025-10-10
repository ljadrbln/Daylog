import type {HttpClient} from '@src/Infrastructure/Http/HttpClient';
import type {Entry} from '@src/Domain/Entries/Entry';
import type {DeleteEntryRequest} from '@src/Application/DTO/Entries/DeleteEntry/DeleteEntryRequest';
import type {DeleteEntryResponse} from '@src/Application/DTO/Entries/DeleteEntry/DeleteEntryResponse';

/**
 * UC-4: Delete Entry (Frontend)
 *
 * Sends DELETE /api/entries/:id and returns response.data as Entry.
 * Minimal GREEN for AC-01: no extra transport/malformed checks yet.
 */
export class DeleteEntryGateway {
    private readonly http: HttpClient;

    constructor(http: HttpClient) {
        this.http = http;
    }

    async delete(req: DeleteEntryRequest): Promise<Entry> {
        const url = `/api/entries/${req.id}`;

        const json = await this.http.request<DeleteEntryResponse>('DELETE', url);
        const result = json.data;

        return result;
    }
}
