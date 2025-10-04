import {type HttpTestCtxBase, createHttpCtx} from '@tests/Unit/Entries/BaseEntriesGatewayTest';
import {HttpEntriesGateway} from '@src/Infrastructure/Entries/ListEntriesGateway';

export type GatewayTestCtx = HttpTestCtxBase & {
    gw: HttpEntriesGateway;
};

/**
 * Provides a fresh ListEntriesGateway built over shared HTTP test context.
 * The caller is responsible for calling ctx.cleanup() in afterEach().
 */
export function createGateway(baseUrl: string = 'http://localhost'): GatewayTestCtx {
    const base = createHttpCtx(baseUrl);
    const gw = new HttpEntriesGateway(base.http);

    const ctx: GatewayTestCtx = {
        ...base,
        gw
    };

    return ctx;
}

// Re-export helper for convenience in old tests.
export {mockJsonOnce} from '@tests/Unit/Entries/BaseEntriesGatewayTest';
