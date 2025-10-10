import {
    type HttpTestCtxBase,
    makeGatewayCtx,
    mockJsonOnce
} from '@tests/Unit/Entries/BaseEntriesGatewayTest';
import {DeleteEntryGateway} from '@src/Infrastructure/Entries/DeleteEntryGateway';

export type GatewayTestCtx = HttpTestCtxBase & {
    gateway: DeleteEntryGateway;
};

/**
 * Provides a mocked DeleteEntryGateway built over the shared HTTP test context.
 */
export function createGateway(baseUrl: string = 'http://localhost'): GatewayTestCtx {
    const ctx = makeGatewayCtx(DeleteEntryGateway, baseUrl);
    return ctx as GatewayTestCtx;
}

export {mockJsonOnce};
