import {
    type HttpTestCtxBase,
    makeGatewayCtx,
    mockJsonOnce
} from '@tests/Unit/Entries/BaseEntriesGatewayTest';
import {UpdateEntryGateway} from '@src/Infrastructure/Entries/UpdateEntryGateway';

export type GatewayTestCtx = HttpTestCtxBase & {
    gateway: UpdateEntryGateway;
};

/**
 * Provides a mocked UpdateEntryGateway built over shared HTTP test context.
 *
 * Mechanics:
 * - Uses common makeGatewayCtx(...) to wire gateway over stubbed HttpClient.
 * - Re-exports mockJsonOnce to enqueue one transport envelope per test.
 */
export function createGateway(baseUrl: string = 'http://localhost'): GatewayTestCtx {
    const ctx = makeGatewayCtx(UpdateEntryGateway, baseUrl);
    return ctx as GatewayTestCtx;
}

export {mockJsonOnce};
