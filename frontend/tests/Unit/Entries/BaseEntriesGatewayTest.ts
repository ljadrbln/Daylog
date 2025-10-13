import {vi, expect} from 'vitest';
import {FetchHttpClient} from '@src/Infrastructure/Http/FetchHttpClient';

export type HttpTestCtxBase = {
    fetchMock: ReturnType<typeof vi.fn>;
    httpClient: FetchHttpClient;
    cleanup: () => void;
};

/**
 * Creates a fresh HTTP test context with a stubbed global.fetch and FetchHttpClient.
 */
export function createHttpCtx(baseUrl: string = 'http://localhost'): HttpTestCtxBase {
    const fetchMock = vi.fn();
    vi.stubGlobal('fetch', fetchMock);

    const httpClient = new FetchHttpClient(baseUrl);

    const cleanup = (): void => {
        vi.unstubAllGlobals();
        vi.clearAllMocks();
    };

    const httpCtx: HttpTestCtxBase = {fetchMock, httpClient, cleanup};

    return httpCtx;
}

/**
 * Type for a gateway class with constructor that accepts FetchHttpClient.
 */
// eslint-disable-next-line no-unused-vars
type HttpGatewayCtor<T> = new (http: FetchHttpClient) => T;

/**
 * Builds a typed gateway test context over the shared HTTP mock context.
 */
export function makeGatewayCtx<T>(
    Gateway: HttpGatewayCtor<T>,
    baseUrl: string = 'http://localhost'
): HttpTestCtxBase & {gateway: T} {
    const httpCtx = createHttpCtx(baseUrl);
    const gateway = new Gateway(httpCtx.httpClient);

    const ctx: HttpTestCtxBase & {gateway: T} = {
        ...httpCtx,
        gateway
    };

    return ctx;
}

/**
 * Enqueue a single JSON response with given status and correct content-type.
 */
export function mockJsonOnce(
    fetchMock: HttpTestCtxBase['fetchMock'],
    status: number,
    body: unknown
): void {
    const res = new Response(JSON.stringify(body), {
        status,
        headers: {
            'content-type': 'application/json'
        }
    });

    fetchMock.mockResolvedValueOnce(res);
}

/**
 * Enqueue a single rejected fetch call (e.g., network failure or abort).
 *
 * Purpose:
 * - Simulate low-level transport errors (TypeError, AbortError, etc.).
 * - Keeps consistent typing with other fetch-mocking helpers.
 *
 * @param {HttpTestCtxBase['fetchMock']} fetchMock Mocked fetch function from test ctx.
 * @param {Error} error Error instance to reject with (e.g., TypeError('Network error')).
 *
 * @returns {void}
 */
export function mockRejectOnce(fetchMock: HttpTestCtxBase['fetchMock'], error: Error): void {
    fetchMock.mockRejectedValueOnce(error);
}

/**
 * Narrows any `{ success: boolean }` union to the success branch.
 * Uses test assertion so failures are reported by the test runner.
 *
 * Example:
 *   const res = makeResponse(req);
 *   assertSuccess(res); // from here TS sees res as `{ success: true; ... }`
 *
 * @param r The response-like object with a boolean `success` flag
 */
export function assertSuccess<T extends {success: boolean}>(
    r: T
): asserts r is T & {success: true} {
    expect(r.success).toBe(true);
}
