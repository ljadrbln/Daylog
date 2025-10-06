import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {createGateway, mockJsonOnce, type GatewayTestCtx} from './BaseListEntriesGatewayTest';
import {okList} from '@tests/helpers/api-responses/UC-2-ListEntries';
import {ListEntriesDataset} from '@tests/helpers/datasets/Entries/ListEntriesDataset';

describe('AC01 — HttpListEntriesGateway returns items on 200 JSON { data.items: [...] }', () => {
    let ctx: GatewayTestCtx;

    beforeEach(() => {
        ctx = createGateway();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('returns items with default sort by date DESC', async () => {
        const items = ListEntriesDataset.ac01HappyPath();
        const payload = okList(items);

        mockJsonOnce(ctx.fetchMock, 200, payload);

        // gw.list returns Entry[]
        const list = await ctx.gw.list();

        expect(list.length).toBe(items.length);
        expect(list[0].id).toBe(items[0].id);
        expect(list[1].id).toBe(items[1].id);
    });
});
