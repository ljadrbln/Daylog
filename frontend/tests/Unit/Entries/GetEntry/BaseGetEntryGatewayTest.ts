import {
    type HttpTestCtxBase,
    makeGatewayCtx,
    mockJsonOnce
} from '@tests/Unit/Entries/BaseEntriesGatewayTest';
import {GetEntryGateway} from '@src/Infrastructure/Entries/GetEntryGateway';

export type GatewayTestCtx = HttpTestCtxBase & {
    gw: GetEntryGateway;
};

/**
 * Provides a mocked GetEntryGateway built over the shared HTTP test context.
 * The caller is responsible for calling ctx.cleanup() in afterEach().
 */
export function createGateway(baseUrl: string = 'http://localhost'): GatewayTestCtx {
    const ctx = makeGatewayCtx(GetEntryGateway, baseUrl);

    return ctx as GatewayTestCtx;
}

export {mockJsonOnce};
