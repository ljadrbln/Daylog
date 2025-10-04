import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {createGateway, mockJsonOnce, type GatewayTestCtx} from './BaseGetEntryGatewayTest';
import {okGet} from '@tests/helpers/api-responses/UC-3-GetEntry';

describe('UC-3 / AC-01 — HttpGetEntryGateway.get(id) returns Entry on 200 JSON { success: true, data.item: {...} }', () => {
    let ctx: GatewayTestCtx;

    beforeEach(() => {
        ctx = createGateway();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('returns an Entry when API responds with success=true and data.item object', async () => {
        const item = {
            id: '23d90e4f-e736-4260-8c31-ae9124fb9280',
            title: 'Valid title',
            body: 'Valid body',
            date: '2025-02-12',
            createdAt: '2025-10-04T15:01:18+00:00',
            updatedAt: '2025-10-04T15:01:18+00:00'
        };

        const payload = okGet(item);
        mockJsonOnce(ctx.fetchMock, 200, payload);

        const entry = await ctx.gw.get(item.id);

        expect(entry.id).toBe(item.id);
        expect(entry.title).toBe('Valid title');
        expect(entry.body).toBe('Valid body');
    });
});
