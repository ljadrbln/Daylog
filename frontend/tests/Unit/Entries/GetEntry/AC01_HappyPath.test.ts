// frontend/tests/Unit/Entries/GetEntry/Http/AC01_HappyPath.test.ts
import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {createGateway, mockJsonOnce, type GatewayTestCtx} from './BaseGetEntryGatewayTest';
import {okGet} from '@tests/helpers/api-responses/UC-3-GetEntry';

describe('AC01 — HttpGetEntryGateway returns entry on 200 JSON { data.item: {...} }', () => {
    let ctx: GatewayTestCtx;

    beforeEach(() => {
        ctx = createGateway();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('returns entry when API responds with { success: true, data.item: {...} }', async () => {
        const payload = okGet({
            id: '23d90e4f-e736-4260-8c31-ae9124fb9280',
            title: 'Valid title',
            body: 'Valid body',
            date: '2025-02-12',
            createdAt: '2025-10-04T15:01:18+00:00',
            updatedAt: '2025-10-04T15:01:18+00:00'
        });

        mockJsonOnce(ctx.fetchMock, 200, payload);

        const entry = await ctx.gw.get(payload.data!.item.id);

        expect(entry.id).toBe('23d90e4f-e736-4260-8c31-ae9124fb9280');
        expect(entry.title).toBe('Valid title');
        expect(entry.body).toBe('Valid body');
    });
});
