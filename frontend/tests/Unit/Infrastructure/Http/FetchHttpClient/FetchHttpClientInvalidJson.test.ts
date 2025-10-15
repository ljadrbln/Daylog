import {describe, it, expect, beforeEach, afterEach, vi} from 'vitest';
import {FetchHttpClient} from '@src/Infrastructure/Http/FetchHttpClient';
import {makeProbeUrl} from '@tests/helpers/http/client/makeProbeUrl';

/**
 * Invalid JSON: FetchHttpClient rejects when response body is not valid JSON.
 *
 * Purpose:
 * Verify that a 200 OK with application/json but invalid JSON body results
 * in a thrown SyntaxError bubbling out of request().
 *
 * Mechanics:
 * - Stub fetch to resolve with Response(200, content-type: application/json).
 * - Body contains invalid JSON (e.g., '<<< not json >>>').
 * - Expect client.request(...) to reject with SyntaxError/message.
 *
 * @covers FetchHttpClient
 */
describe('FetchHttpClient — Invalid JSON (200 OK, application/json)', () => {
    let fetchMock: ReturnType<typeof vi.fn>;

    beforeEach(() => {
        fetchMock = vi.fn();
        vi.stubGlobal('fetch', fetchMock);
    });

    afterEach(() => {
        vi.unstubAllGlobals();
        vi.clearAllMocks();
    });

    it('rejects with SyntaxError when body is not valid JSON', async () => {
        // Arrange
        const baseUrl = 'http://localhost';
        const client = new FetchHttpClient(baseUrl);

        const invalid = '<<< not json >>>';
        const res = new Response(invalid, {
            status: 200,
            headers: {'content-type': 'application/json'}
        });

        fetchMock.mockResolvedValueOnce(res);

        // Act
        const url = makeProbeUrl('HTTP', 'InvalidJson');
        const run = client.request('GET', url);

        // Assert
        await expect(run).rejects.toThrow(SyntaxError);
        await expect(run).rejects.toThrow(/json|parse|unexpected/i);
    });
});
