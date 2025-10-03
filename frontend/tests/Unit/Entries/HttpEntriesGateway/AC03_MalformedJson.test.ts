import { describe, it, expect, beforeEach, afterEach } from 'vitest';
import { createGateway, mockJsonOnce, type GatewayTestCtx } from './BaseHttpEntriesGatewayTest';
import { okMalformedListWithoutItems } from '@tests/helpers/api-responses/UC-2-ListEntries';

describe('AC03 — HttpEntriesGateway throws on malformed JSON shape (no data.items)', () => {
    let ctx: GatewayTestCtx;

    beforeEach(() => {
        ctx = createGateway();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('throws when API returns JSON without items array', async () => {
        mockJsonOnce(ctx.fetchMock, 200, okMalformedListWithoutItems);

        await expect(ctx.gw.list()).rejects.toThrow(/malformed|items/i);
    });

    it('throws when success=false even with 200', async () => {
        mockJsonOnce(ctx.fetchMock, 200, {
            success: false,
            data: {
                items: [],
            },
            status: 200,
        });

        await expect(ctx.gw.list()).rejects.toThrow(/malformed|success/i);
    });
});
