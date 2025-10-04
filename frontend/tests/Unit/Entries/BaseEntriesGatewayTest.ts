// BaseHttpGatewayTest.ts — общий базовый хелпер для HTTP-тестов (любой gateway)
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
