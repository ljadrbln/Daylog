import {type HttpTestCtxBase, createHttpCtx} from '@tests/Unit/Entries/BaseEntriesGatewayTest';
import {GetEntryGateway} from '@src/Infrastructure/Entries/GetEntryGateway';

export type GatewayTestCtx = HttpTestCtxBase & {
    gw: GetEntryGateway;
};

/**
 * Provides a fresh GetEntryGateway built over shared HTTP test context.
 * The caller is responsible for calling ctx.cleanup() in afterEach().
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
