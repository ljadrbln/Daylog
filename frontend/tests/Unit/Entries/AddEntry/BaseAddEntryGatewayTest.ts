import {type HttpTestCtxBase, createHttpCtx} from '@tests/Unit/Entries/BaseEntriesGatewayTest';
import {AddEntryGateway} from '@src/Infrastructure/Entries/AddEntryGateway';

export type GatewayTestCtx = HttpTestCtxBase & {
    gw: AddEntryGateway;
};

/**
 * Provides a mocked AddEntryGateway built over shared HTTP test context.
 * The caller is responsible for calling ctx.cleanup() in afterEach().
 */
export function createGateway(baseUrl: string = 'http://localhost'): GatewayTestCtx {
    const base = createHttpCtx(baseUrl);
    const gw = new AddEntryGateway(base.http);

    const ctx: GatewayTestCtx = {
        ...base,
        gw
    };

    return ctx;
}

export {mockJsonOnce} from '@tests/Unit/Entries/BaseEntriesGatewayTest';
