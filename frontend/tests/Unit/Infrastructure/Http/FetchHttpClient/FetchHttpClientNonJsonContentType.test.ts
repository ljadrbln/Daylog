import {describe, it, expect, beforeEach, afterEach, vi} from 'vitest';
import {FetchHttpClient} from '@src/Infrastructure/Http/FetchHttpClient';
import {makeProbeUrl} from '@tests/helpers/http/client/makeProbeUrl';

/**
 * Non-JSON Content-Type: FetchHttpClient rejects when response
 * header is not application/json.
 *
 * Purpose:
 * Verify that a 200 OK response with a non-JSON content-type (e.g. text/html)
 * results in a thrown error while attempting to parse or detect malformed content.
 *
 * Mechanics:
 * - Stub fetch to resolve with Response(200, content-type: text/html).
 * - Body contains plain text (not JSON).
 * - Expect client.request(...) to reject with an error mentioning 'content-type' or 'JSON'.
 *
 * @covers FetchHttpClient
 */
describe('FetchHttpClient — Non-JSON Content-Type (200 OK, text/html)', () => {
    let fetchMock: ReturnType<typeof vi.fn>;

    beforeEach(() => {
        fetchMock = vi.fn();
        vi.stubGlobal('fetch', fetchMock);
    });

    afterEach(() => {
        vi.unstubAllGlobals();
        vi.clearAllMocks();
    });

    it('rejects when Content-Type is not application/json', async () => {
        const baseUrl = 'http://localhost';
        const client = new FetchHttpClient(baseUrl);

        const html = '<html><body>OK</body></html>';
        const res = new Response(html, {
            status: 200,
            headers: {'content-type': 'text/html'}
        });
        fetchMock.mockResolvedValueOnce(res);

        const url = makeProbeUrl('HTTP', 'NonJson');
        const run = client.request('GET', url);

        await expect(run).rejects.toThrow(/json|content|parse/i);
    });
});
