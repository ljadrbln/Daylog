import {describe, it, expect, beforeEach, afterEach, vi} from 'vitest';
import {FetchHttpClient} from '@src/Infrastructure/Http/FetchHttpClient';
import {makeProbeUrl} from '@tests/helpers/http/client/makeProbeUrl';

/**
 * Abort: FetchHttpClient rejects when fetch is aborted.
 *
 * Purpose:
 * Verify transport behavior when a request is cancelled (AbortController).
 *
 * Mechanics:
 * - Stub global.fetch to reject with an AbortError-like object.
 * - Ensure client.request(...) rejects and preserves 'abort' in the message.
 *
 * @covers FetchHttpClient
 */
describe('FetchHttpClient — Abort (fetch rejects with AbortError)', () => {
    let fetchMock: ReturnType<typeof vi.fn>;

    beforeEach(() => {
        fetchMock = vi.fn();
        vi.stubGlobal('fetch', fetchMock);
    });

    afterEach(() => {
        vi.unstubAllGlobals();
        vi.clearAllMocks();
    });

    it('rejects when fetch rejects with AbortError', async () => {
        // Arrange
        const baseUrl = 'http://localhost';
        const client = new FetchHttpClient(baseUrl);

        const message = 'Request aborted';
        const error = new Error(message);
        fetchMock.mockRejectedValueOnce(error);

        // Act
        const url = makeProbeUrl('HTTP', 'Abort');
        const run = client.request('GET', url);

        // Assert
        await expect(run).rejects.toThrow(message);
    });
});
