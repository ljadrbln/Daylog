import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {createGateway, mockJsonOnce, type GatewayTestCtx} from './BaseListEntriesGatewayTest';
import {ac03MalformedJson as makeRequest} from '@tests/helpers/http/requests/entries/ListEntriesRequestFactory';
import {ac03MalformedJson as makeResponse} from '@tests/helpers/http/responses/entries/ListEntriesResponseFactory';

/**
 * UC-2: List Entries (Frontend, Gateway)
 *
 * Purpose:
 * Verify that ListEntriesGateway rejects when API responds with a malformed JSON body,
 * i.e. success=true but `data.items` is missing or has wrong shape.
 *
 * Mechanics:
 * - Build a valid request via request factory (no literals).
 * - Build a malformed success response via response factory (no `items`).
 * - Mock HTTP once with JSON payload and status 200.
 * - Assert the gateway rejects with a Malformed* style error.
 *
 * Case:
 * - AC-03 — Malformed JSON (success=true with malformed data).
 */
describe('AC03 — ListEntriesGateway rejects on malformed JSON (missing items)', () => {
    let ctx: GatewayTestCtx;

    beforeEach(() => {
        ctx = createGateway();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('throws when success=true but `data.items` is missing', async () => {
        // prettier-ignore
        const request  = makeRequest();
        const response = makeResponse(request); // success:true, but malformed data (no items)

        mockJsonOnce(ctx.fetchMock, 200, response);

        const fn = ctx.gw.list(request);
        const message = 'Malformed response for GET /api/entries';

        await expect(fn).rejects.toThrowError(message);
    });
});
