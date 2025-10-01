import { describe, it, expect, beforeEach, afterEach } from 'vitest';
import { createGateway, mockJsonOnce, type GatewayTestCtx } from './BaseHttpEntriesGatewayTest';

describe('AC01 — HttpEntriesGateway returns list on 200 JSON { data.items: [...] }', () => {
    let ctx: GatewayTestCtx;

    beforeEach(() => {
        ctx = createGateway();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('returns entries when API responds with { success: true, data.items: [...] }', async () => {
        mockJsonOnce(ctx.fetchMock, 200, {
            success: true,
            data: {
                items: [
                    { id: '1', title: 'First' },
                    { id: '2', title: 'Second' },
                ],
                page: 1,
                perPage: 10,
                total: 2,
                pagesCount: 1,
            },
            status: 200,
        });

        const entries = await ctx.gw.list();

        expect(entries.length).toBe(2);
        expect(entries[0].title).toBe('First');
        expect(entries[1].id).toBe('2');
    });
});
