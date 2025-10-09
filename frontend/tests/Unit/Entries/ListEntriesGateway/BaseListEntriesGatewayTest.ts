import {
    type HttpTestCtxBase,
    makeGatewayCtx,
    mockJsonOnce
} from '@tests/Unit/Entries/BaseEntriesGatewayTest';
import {ListEntriesGateway} from '@src/Infrastructure/Entries/ListEntriesGateway';

export type GatewayTestCtx = HttpTestCtxBase & {
    gateway: ListEntriesGateway;
};

/**
 * Provides a mocked ListEntriesGateway built over the shared HTTP test context.
 */
export function createGateway(baseUrl: string = 'http://localhost'): GatewayTestCtx {
    const ctx = makeGatewayCtx(ListEntriesGateway, baseUrl);
    return ctx as GatewayTestCtx;
}

export {mockJsonOnce};
