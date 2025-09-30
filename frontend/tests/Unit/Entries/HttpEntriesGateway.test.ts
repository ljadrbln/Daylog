import { describe, it, expect, beforeEach, afterEach, vi } from 'vitest';

import { FetchHttpClient } from '../../../src/Infrastructure/Http/FetchHttpClient';
import { HttpEntriesGateway } from '../../../src/Infrastructure/Entries/HttpEntriesGateway';

let fetchMock: ReturnType<typeof vi.fn>;

function mockFetchOnce(body: object) {
    const res = new Response(JSON.stringify(body), {
        status: 200,
        headers: { 'content-type': 'application/json' },
    });
    fetchMock.mockResolvedValueOnce(res);
}

describe('HttpEntriesGateway.list — happy path', () => {
    const baseUrl = 'http://localhost';

    beforeEach(() => {
        fetchMock = vi.fn();
        vi.stubGlobal('fetch', fetchMock);
    });

    afterEach(() => {
        vi.unstubAllGlobals();
        vi.clearAllMocks();
    });

    it('returns entries when API responds with { items:[...] }', async () => {
        mockFetchOnce({
            items: [
                { id: '1', title: 'First' },
                { id: '2', title: 'Second' },
            ],
        });

        const http = new FetchHttpClient(baseUrl);
        const gw = new HttpEntriesGateway(http);

        const entries = await gw.list();

        expect(entries.length).toBe(2);
        expect(entries[0].title).toBe('First');
        expect(entries[1].id).toBe('2');
    });
});
