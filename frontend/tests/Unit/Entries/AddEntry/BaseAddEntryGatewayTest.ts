import {
    type HttpTestCtxBase,
    makeGatewayCtx,
    mockJsonOnce
} from '@tests/Unit/Entries/BaseEntriesGatewayTest';
import {AddEntryGateway} from '@src/Infrastructure/Entries/AddEntryGateway';

export type GatewayTestCtx = HttpTestCtxBase & {
    gw: AddEntryGateway;
};

/**
 * Provides a mocked AddEntryGateway built over shared HTTP test context.
 * The caller is responsible for calling ctx.cleanup() in afterEach().
 */
export function createGateway(baseUrl: string = 'http://localhost'): GatewayTestCtx {
    const ctx = makeGatewayCtx(AddEntryGateway, baseUrl);

    return ctx as GatewayTestCtx;
}

export {mockJsonOnce};
