import {type HttpTestCtxBase, createHttpCtx} from '@tests/Unit/Entries/BaseEntriesGatewayTest';
import {GetEntryGateway} from '@src/Infrastructure/Entries/GetEntryGateway';

export type GatewayTestCtx = HttpTestCtxBase & {
    gw: GetEntryGateway;
};

/**
 * Provides a mocked GetEntryGateway built over the shared HTTP test context.
 * The caller is responsible for calling ctx.cleanup() in afterEach().
 *
 * Mechanics:
 * - Reuses the base HTTP mock context (fetchMock, http, cleanup).
 * - Builds a gateway instance over the mocked HttpClient.
 * - Re-exports mock helpers from the common base for consistency.
 */
export function createGateway(baseUrl: string = 'http://localhost'): GatewayTestCtx {
    const base = createHttpCtx(baseUrl);
    const gw = new GetEntryGateway(base.http);

    const ctx: GatewayTestCtx = {
        ...base,
        gw
    };

    return ctx;
}

export {mockJsonOnce} from '@tests/Unit/Entries/BaseEntriesGatewayTest';
