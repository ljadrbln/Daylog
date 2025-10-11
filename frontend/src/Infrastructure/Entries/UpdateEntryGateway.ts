import type {HttpClient} from '@src/Infrastructure/Http/HttpClient';
import type {Entry} from '@src/Domain/Entries/Entry';
import type {UpdateEntryRequest} from '@src/Application/DTO/Entries/UpdateEntry/UpdateEntryRequest';

/**
 * UC-5: Update Entry (Frontend, Gateway)
 *
 * Placeholder stub used for RED phase in TDD.
 * Implements the interface shape and throws until real logic is written.
 */
export class UpdateEntryGateway {
    private readonly http: HttpClient;

    constructor(http: HttpClient) {
        this.http = http;
    }

    /**
     * Temporary stub for TDD RED phase.
     *
     * @param {UpdateEntryRequest} _req UpdateEntry request payload.
     * @throws {Error} Always, until implemented.
     */
    async update(_req: UpdateEntryRequest): Promise<Entry> {
        void _req;
        const message = 'Not implemented: UC-5 UpdateEntryGateway.update()';
        throw new Error(message);
    }
}
