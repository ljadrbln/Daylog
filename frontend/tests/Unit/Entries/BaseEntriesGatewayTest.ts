import {vi} from 'vitest';
import {FetchHttpClient} from '@src/Infrastructure/Http/FetchHttpClient';

export type HttpTestCtxBase = {
    fetchMock: ReturnType<typeof vi.fn>;
    http: FetchHttpClient;
    cleanup: () => void;
};

/**
 * Creates a fresh HTTP test context with a stubbed global.fetch and FetchHttpClient.
 * The caller must call ctx.cleanup() in afterEach().
 */
export function createHttpCtx(baseUrl: string = 'http://localhost'): HttpTestCtxBase {
    const fetchMock = vi.fn();
    vi.stubGlobal('fetch', fetchMock);

    const http = new FetchHttpClient(baseUrl);

    const cleanup = (): void => {
        vi.unstubAllGlobals();
        vi.clearAllMocks();
    };

    const ctx: HttpTestCtxBase = {fetchMock, http, cleanup};
    return ctx;
}

/**
 * Type for a gateway class with constructor that accepts FetchHttpClient.
 */
export type HttpGatewayCtor<T> = new (http: FetchHttpClient) => T;

/**
 * Build a typed gateway test context over the shared HTTP mock context.
 * Keeps the same outward shape used in per-gateway helpers: { ...HttpTestCtxBase, gw: T }.
 * Non-breaking for dependent tests that rely on 'gw' property.
 */
export function makeGatewayCtx<T>(
    Gateway: HttpGatewayCtor<T>,
    baseUrl: string = 'http://localhost'
): HttpTestCtxBase & {gw: T} {
    const base = createHttpCtx(baseUrl);
    const gw = new Gateway(base.http);

    const ctx: HttpTestCtxBase & {gw: T} = {
        ...base,
        gw
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
