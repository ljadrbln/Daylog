import { describe, it, expect, beforeEach, afterEach } from 'vitest';
import { createGateway, type GatewayTestCtx } from './BaseHttpEntriesGatewayTest';

describe('AC02 — HttpEntriesGateway throws on non-2xx response', () => {
    let ctx: GatewayTestCtx;

    beforeEach(() => {
        ctx = createGateway();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('throws when API responds 500', async () => {
        const res = new Response('{}', {
            status: 500,
            headers: {
                'content-type': 'application/json',
            },
        });

        ctx.fetchMock.mockResolvedValueOnce(res);

        await expect(ctx.gw.list()).rejects.toThrow(/HTTP 500/);
    });
});
