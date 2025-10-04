import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {createGateway, mockJsonOnce, type GatewayTestCtx} from './BaseHttpEntriesGatewayTest';
import {okList} from '@tests/helpers/api-responses/UC-2-ListEntries';

describe('AC01 — HttpEntriesGateway returns list on 200 JSON { data.items: [...] }', () => {
    let ctx: GatewayTestCtx;

    beforeEach(() => {
        ctx = createGateway();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('returns entries when API responds with { success: true, data.items: [...] }', async () => {
        const payload = okList([
            {id: '1', title: 'First'},
            {id: '2', title: 'Second'}
        ]);

        mockJsonOnce(ctx.fetchMock, 200, payload);

        const entries = await ctx.gw.list();

        expect(entries.length).toBe(2);
        expect(entries[0].title).toBe('First');
        expect(entries[1].id).toBe('2');
    });
});
