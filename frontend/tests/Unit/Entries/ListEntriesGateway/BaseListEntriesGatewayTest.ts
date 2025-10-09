import {type HttpTestCtxBase, createHttpCtx} from '@tests/Unit/Entries/BaseEntriesGatewayTest';
import {ListEntriesGateway} from '@src/Infrastructure/Entries/ListEntriesGateway';

export type GatewayTestCtx = HttpTestCtxBase & {
    gw: ListEntriesGateway;
};

/**
 * Provides a mocked ListEntriesGateway built over the shared HTTP test context.
 * The caller must call ctx.cleanup() in afterEach().
 *
 * Mechanics:
 * - Reuses the base HTTP mock context (fetchMock, http, cleanup).
 * - Builds a gateway instance over the mocked HttpClient.
 * - Re-exports mock helpers for consistency across gateway suites.
 */
export function createGateway(baseUrl: string = 'http://localhost'): GatewayTestCtx {
    const base = createHttpCtx(baseUrl);
    const gw = new ListEntriesGateway(base.http);

    const ctx: GatewayTestCtx = {
        ...base,
        gw
    };

    return ctx;
}

export {mockJsonOnce} from '@tests/Unit/Entries/BaseEntriesGatewayTest';
