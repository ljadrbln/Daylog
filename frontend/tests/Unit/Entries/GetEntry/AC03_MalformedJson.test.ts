import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {createGateway, mockJsonOnce, type GatewayTestCtx} from './BaseGetEntryGatewayTest';
import {okMalformedGetWithoutItem} from '@tests/helpers/api-responses/UC-3-GetEntry';

describe('AC03 — HttpGetEntryGateway throws on malformed JSON (no data.item)', () => {
    let ctx: GatewayTestCtx;

    beforeEach(() => {
        ctx = createGateway();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('throws when API returns JSON without data.item object', async () => {
        mockJsonOnce(ctx.fetchMock, 200, okMalformedGetWithoutItem());

        const fn = ctx.gw.get('any-id');
        const message = 'Malformed response for GET /api/entries/:id';

        await expect(fn).rejects.toThrow(message);
    });
});
