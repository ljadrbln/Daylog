import {
    type HttpTestCtxBase,
    makeGatewayCtx,
    mockJsonOnce
} from '@tests/Unit/Entries/BaseEntriesGatewayTest';
import {GetEntryGateway} from '@src/Infrastructure/Entries/GetEntryGateway';

export type GatewayTestCtx = HttpTestCtxBase & {
    gateway: GetEntryGateway;
};

/**
 * Provides a mocked GetEntryGateway built over the shared HTTP test context.
 */
export function createGateway(baseUrl: string = 'http://localhost'): GatewayTestCtx {
    const ctx = makeGatewayCtx(GetEntryGateway, baseUrl);
    return ctx as GatewayTestCtx;
}

export {mockJsonOnce};
