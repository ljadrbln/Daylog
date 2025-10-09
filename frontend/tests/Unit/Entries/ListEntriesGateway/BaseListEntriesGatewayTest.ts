import {
    type HttpTestCtxBase,
    makeGatewayCtx,
    mockJsonOnce
} from '@tests/Unit/Entries/BaseEntriesGatewayTest';
import {ListEntriesGateway} from '@src/Infrastructure/Entries/ListEntriesGateway';

export type GatewayTestCtx = HttpTestCtxBase & {
    gw: ListEntriesGateway;
};

/**
 * Provides a mocked ListEntriesGateway built over the shared HTTP test context.
 * The caller must call ctx.cleanup() in afterEach().
 */
export function createGateway(baseUrl: string = 'http://localhost'): GatewayTestCtx {
    const ctx = makeGatewayCtx(ListEntriesGateway, baseUrl);

    return ctx as GatewayTestCtx;
}

export {mockJsonOnce};
