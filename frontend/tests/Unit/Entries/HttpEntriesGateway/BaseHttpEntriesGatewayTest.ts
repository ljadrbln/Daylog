import { vi } from 'vitest';
import { FetchHttpClient } from '../../../../src/Infrastructure/Http/FetchHttpClient';
import { HttpEntriesGateway } from '../../../../src/Infrastructure/Entries/HttpEntriesGateway';

export type GatewayTestCtx = {
    fetchMock: ReturnType<typeof vi.fn>;
    http: FetchHttpClient;
    gw: HttpEntriesGateway;
    cleanup: () => void;
};

/**
 * Provides a fresh HttpEntriesGateway and a stubbed global.fetch for each test.
 * The caller is responsible for calling ctx.cleanup() in afterEach().
 */
export function createGateway(baseUrl: string = 'http://localhost'): GatewayTestCtx {
    const fetchMock = vi.fn();
    vi.stubGlobal('fetch', fetchMock);

    const http = new FetchHttpClient(baseUrl);
    const gw = new HttpEntriesGateway(http);

    const cleanup = (): void => {
        vi.unstubAllGlobals();
        vi.clearAllMocks();
    };

    const ctx: GatewayTestCtx = { fetchMock, http, gw, cleanup };
    return ctx;
}

/**
 * Convenience helper: enqueue a single JSON response with given status.
 * Validates via content-type to mimic real API behavior.
 */
export function mockJsonOnce(
    fetchMock: GatewayTestCtx['fetchMock'],
    status: number,
    body: unknown,
): void {
    const res = new Response(JSON.stringify(body), {
        status,
        headers: { 'content-type': 'application/json' },
    });
    fetchMock.mockResolvedValueOnce(res);
}
