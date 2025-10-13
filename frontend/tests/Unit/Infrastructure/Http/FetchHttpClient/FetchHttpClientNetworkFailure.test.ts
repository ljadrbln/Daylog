import {describe, it, expect, beforeEach, afterEach, vi} from 'vitest';
import {FetchHttpClient} from '@src/Infrastructure/Http/FetchHttpClient';
import {makeProbeUrl} from '@tests/helpers/http/client/makeProbeUrl';

/**
 * AC-05: Network failure — FetchHttpClient rejects when fetch itself rejects.
 *
 * Purpose:
 * Verify transport behavior on low-level network errors (e.g., offline/DNS).
 *
 * Mechanics:
 * - Stub global.fetch to reject with TypeError('Network error').
 * - Ensure client.request(...) rejects and bubbles a meaningful message.
 *
 * @covers FetchHttpClient
 */
describe('FetchHttpClient — Network Failure (fetch rejects)', () => {
    let fetchMock: ReturnType<typeof vi.fn>;

    beforeEach(() => {
        fetchMock = vi.fn();
        vi.stubGlobal('fetch', fetchMock);
    });

    afterEach(() => {
        vi.unstubAllGlobals();
        vi.clearAllMocks();
    });

    it('rejects when fetch rejects (TypeError: Network error)', async () => {
        const baseUrl = 'http://localhost';
        const client = new FetchHttpClient(baseUrl);

        const message = 'Network error';
        const error = new TypeError(message);
        fetchMock.mockRejectedValueOnce(error);

        const url = makeProbeUrl('HTTP', 'NetworkFailure');
        const run = client.request('GET', url);
        await expect(run).rejects.toThrow(message);
    });
});
