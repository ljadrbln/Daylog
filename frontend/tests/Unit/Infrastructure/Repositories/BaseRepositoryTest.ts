import {vi, expect} from 'vitest';
import {FetchHttpClient} from '@src/Infrastructure/Http/FetchHttpClient';

/**
 * Base HTTP context for all repository tests.
 */
export type HttpTestCtxBase = {
    fetchMock: ReturnType<typeof vi.fn>;
    httpClient: FetchHttpClient;
    cleanup: () => void;
};

/**
 * Creates a fresh HTTP context with stubbed global.fetch and FetchHttpClient.
 */
export function createHttpCtx(baseUrl: string = 'http://localhost'): HttpTestCtxBase {
    const fetchMock = vi.fn();
    vi.stubGlobal('fetch', fetchMock);

    const httpClient = new FetchHttpClient(baseUrl);

    const cleanup = (): void => {
        vi.unstubAllGlobals();
        vi.clearAllMocks();
    };

    return {fetchMock, httpClient, cleanup};
}

/**
 * Enqueue a single JSON response with given status and proper content-type.
 */
export function mockJsonOnce(
    fetchMock: HttpTestCtxBase['fetchMock'],
    status: number,
    body: unknown
): void {
    const headers = {'content-type': 'application/json'};
    const response = JSON.stringify(body);

    const res = new Response(response, {status, headers});
    fetchMock.mockResolvedValueOnce(res);
}

/**
 * Enqueue a single rejected fetch call (e.g., network failure or abort).
 */
export function mockRejectOnce(fetchMock: HttpTestCtxBase['fetchMock'], error: Error): void {
    fetchMock.mockRejectedValueOnce(error);
}

/**
 * Narrows any `{ success: boolean }` union to the success branch.
 */
export function assertSuccess<T extends {success: boolean}>(
    r: T
): asserts r is T & {success: true} {
    expect(r.success).toBe(true);
}
