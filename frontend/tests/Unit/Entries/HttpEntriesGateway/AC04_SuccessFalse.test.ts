import { describe, it, expect, beforeEach, afterEach } from 'vitest';
import { createGateway, mockJsonOnce, type GatewayTestCtx } from './BaseHttpEntriesGatewayTest';
import { successFalse } from '@tests/helpers/api-responses/UC-2-ListEntries';

describe('AC04 — HttpEntriesGateway throws when success=false (even with 200)', () => {
    let ctx: GatewayTestCtx;

    beforeEach(() => {
        ctx = createGateway();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('throws on success=false', async () => {
        mockJsonOnce(ctx.fetchMock, 200, successFalse);

        await expect(ctx.gw.list()).rejects.toThrow(/malformed|success/i);
    });
});
