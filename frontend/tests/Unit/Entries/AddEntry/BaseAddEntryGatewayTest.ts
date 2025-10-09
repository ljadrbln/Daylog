import {
    type HttpTestCtxBase,
    makeGatewayCtx,
    mockJsonOnce
} from '@tests/Unit/Entries/BaseEntriesGatewayTest';
import {AddEntryGateway} from '@src/Infrastructure/Entries/AddEntryGateway';

export type GatewayTestCtx = HttpTestCtxBase & {
    gateway: AddEntryGateway;
};

/**
 * Provides a mocked AddEntryGateway built over shared HTTP test context.
 */
export function createGateway(baseUrl: string = 'http://localhost'): GatewayTestCtx {
    const ctx = makeGatewayCtx(AddEntryGateway, baseUrl);
    return ctx as GatewayTestCtx;
}

export {mockJsonOnce};
