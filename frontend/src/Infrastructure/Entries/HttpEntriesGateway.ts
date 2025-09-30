import type { HttpClient } from '../Http/FetchHttpClient';

type Entry = {
    id: string;
    title: string;
};

type EntriesListResponse = {
    items: Entry[];
};

export class HttpEntriesGateway {
    private readonly http: HttpClient;

    constructor(http: HttpClient) {
        this.http = http;
    }

    async list(): Promise<Entry[]> {
        const json = await this.http.request<EntriesListResponse>('GET', '/api/entries');

        if (!json || !Array.isArray(json.items)) {
            const message = 'Malformed response for GET /api/entries';
            throw new Error(message);
        }

        return json.items;
    }
}
