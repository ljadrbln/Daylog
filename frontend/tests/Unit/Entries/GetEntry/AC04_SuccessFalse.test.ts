import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {createGateway, mockJsonOnce, type GatewayTestCtx} from './BaseGetEntryGatewayTest';
import {successFalseGet} from '@tests/helpers/api-responses/UC-3-GetEntry';

describe('AC04 — HttpGetEntryGateway throws when success=false even on 200 OK', () => {
    let ctx: GatewayTestCtx;

    beforeEach(() => {
        ctx = createGateway();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('throws when API returns 200 OK but success=false', async () => {
        const payload = successFalseGet({
            id: '23d90e4f-e736-4260-8c31-ae9124fb9280',
            title: 'Valid title',
            body: 'Valid body',
            date: '2025-02-12',
            createdAt: '2025-10-04T15:01:18+00:00',
            updatedAt: '2025-10-04T15:01:18+00:00'
        });

        mockJsonOnce(ctx.fetchMock, 200, payload);

        const fn = ctx.gw.get('23d90e4f-e736-4260-8c31-ae9124fb9280');
        const message = 'Malformed response for GET /api/entries/:id';

        await expect(fn).rejects.toThrow(message);
    });
});
