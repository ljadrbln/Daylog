import type {HttpClient} from '@src/Infrastructure/Http/HttpClient';
import type {Entry} from '@src/Domain/Entries/Entry';
import type {DeleteEntryRequest} from '@src/Application/DTO/Entries/DeleteEntry/DeleteEntryRequest';

/**
 * UC-4: Delete Entry (Frontend)
 *
 * Sends DELETE /api/entries/:id.
 * This is a RED-phase stub: not implemented yet.
 */
export class DeleteEntryGateway {
    private readonly http: HttpClient;

    constructor(http: HttpClient) {
        this.http = http;
    }

    async delete(_req: DeleteEntryRequest): Promise<Entry> {
        void _req;

        const message = 'DeleteEntryGateway.delete: not implemented (RED)';
        throw new Error(message);
    }
}
