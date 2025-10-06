import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {createGateway, type GatewayTestCtx} from './BaseGetEntryGatewayTest';

describe('AC02 — HttpGetEntryGateway throws on non-2xx response', () => {
    let ctx: GatewayTestCtx;

    beforeEach(() => {
        ctx = createGateway();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('throws when API responds 404', async () => {
        const res = new Response('{}', {
            status: 404,
            headers: {
                'content-type': 'application/json'
            }
        });

        ctx.fetchMock.mockResolvedValueOnce(res);

        const fn = ctx.gw.get('missing-id');
        const message = /HTTP 404/;

        await expect(fn).rejects.toThrow(message);
    });

    it('throws when API responds 500', async () => {
        const res = new Response('{}', {
            status: 500,
            headers: {
                'content-type': 'application/json'
            }
        });

        ctx.fetchMock.mockResolvedValueOnce(res);

        const fn = ctx.gw.get('any');
        const message = /HTTP 500/;

        await expect(fn).rejects.toThrow(message);
    });
});
