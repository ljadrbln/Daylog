import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {createGateway, mockJsonOnce, type GatewayTestCtx} from './BaseGetEntryGatewayTest';
import {GetEntryDataset} from '@tests/helpers/datasets/Entries/GetEntryDataset';
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
        const item = GetEntryDataset.ac04SuccessFalse();
        const payload = successFalseGet(item);

        mockJsonOnce(ctx.fetchMock, 200, payload);

        const fn = ctx.gw.get(item.id);
        const message = 'Malformed response for GET /api/entries/:id';

        await expect(fn).rejects.toThrow(message);
    });
});
