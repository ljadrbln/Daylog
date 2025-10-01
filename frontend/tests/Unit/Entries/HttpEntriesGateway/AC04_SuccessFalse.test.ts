import { describe, it, expect, beforeEach, afterEach } from 'vitest';
import { createGateway, mockJsonOnce, type GatewayTestCtx } from './BaseHttpEntriesGatewayTest';

describe('AC04 — HttpEntriesGateway throws when success=false (even with 200)', () => {
    let ctx: GatewayTestCtx;

    beforeEach(() => {
        ctx = createGateway();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('throws on success=false', async () => {
        mockJsonOnce(ctx.fetchMock, 200, {
            success: false,
            data: { items: [] , page: 1, perPage: 10, total: 0, pagesCount: 1 },
            status: 200,
            message: 'logical failure'
        });

        await expect(ctx.gw.list())
            .rejects
            .toThrow(/malformed|success/i);
    });
});
