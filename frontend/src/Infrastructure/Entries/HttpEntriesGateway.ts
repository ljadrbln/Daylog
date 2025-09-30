import type { HttpClient } from '../Http/FetchHttpClient';
import type { Entry } from '../../Domain/Entries/Entry';
import type { UseCaseResponse } from '../../Application/DTO/Common/UseCaseResponse';
import type { ListEntriesData } from '../../Application/DTO/Entries/ListEntries/ListEntriesRequest';

export class HttpEntriesGateway {
    private readonly http: HttpClient;

    constructor(http: HttpClient) {
        this.http = http;
    }

    async list(): Promise<Entry[]> {
        const json = await this.http.request<UseCaseResponse<ListEntriesData>>('GET', '/api/entries');

        const ok = json?.success === true;
        const hasItems = Array.isArray(json?.data?.items);
        
        if (!ok || !hasItems) {
            const message = 'Malformed response for GET /api/entries';
            throw new Error(message);
        }

        const items = json.data!.items;
        return items;
    }
}
