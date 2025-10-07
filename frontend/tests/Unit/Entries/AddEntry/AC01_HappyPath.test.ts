import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {createGateway, mockJsonOnce, type GatewayTestCtx} from './BaseAddEntryGatewayTest';
import {okPost} from '@tests/helpers/api-responses/UC-1-AddEntry';

//import {AddEntryRequestDataset} from '@tests/helpers/datasets/Entries/AddEntryRequestDataset';
import {AddEntryDataset} from '@tests/helpers/datasets/Entries/AddEntryDataset';

describe('AC01 — AddEntryGateway returns entry (mocked)', () => {
    let ctx: GatewayTestCtx;

    beforeEach(() => {
        ctx = createGateway();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('returns entry when called with valid payload', async () => {
        const item = AddEntryDataset.ac01HappyPath();
        const payload = okPost(item);

        mockJsonOnce(ctx.fetchMock, 200, payload);
        const entry = await ctx.gw.add(item);

        expect(entry.title).toBe(item.title);
        expect(entry.body).toBe(item.body);
        expect(entry.date).toBe(item.date);
        expect(entry.createdAt <= entry.updatedAt).toBe(true);
    });
});
