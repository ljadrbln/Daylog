// frontend/tests/Unit/Entries/GetEntry/Http/AC01_HappyPath.test.ts
import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {createGateway, mockJsonOnce, type GatewayTestCtx} from './BaseGetEntryGatewayTest';
import {okGet} from '@tests/helpers/api-responses/UC-3-GetEntry';
import {GetEntryDataset} from '@tests/helpers/datasets/Entries/GetEntryDataset';

describe('AC01 — HttpGetEntryGateway returns entry on 200 JSON { data.item: {...} }', () => {
    let ctx: GatewayTestCtx;

    beforeEach(() => {
        ctx = createGateway();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('returns entry when API responds with { success: true, data.item: {...} }', async () => {
        const item = GetEntryDataset.ac01HappyPath();
        const payload = okGet(item);

        mockJsonOnce(ctx.fetchMock, 200, payload);

        const entry = await ctx.gw.get(item.id);

        expect(entry.id).toBe(item.id);
        expect(entry.title).toBe(item.title);
        expect(entry.body).toBe(item.body);

        expect(entry.createdAt <= entry.updatedAt).toBe(true);
    });
});
