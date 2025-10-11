import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {createGateway, mockJsonOnce, type GatewayTestCtx} from './BaseListEntriesGatewayTest';
import {ac04SuccessFalse as makeRequest} from '@tests/helpers/http/requests/entries/ListEntriesRequestFactory';
import {ac04SuccessFalse as makeResponse} from '@tests/helpers/http/responses/entries/ListEntriesResponseFactory';

/**
 * UC-2: List Entries (Frontend, Gateway)
 *
 * Purpose:
 * Verify that ListEntriesGateway rejects when API responds with { success:false }
 * even though HTTP status is 200.
 *
 * Mechanics:
 * - Build a valid request via factory (no literals).
 * - Build a mocked response with success=false via response factory.
 * - Mock HTTP once with status 200 and the error-like payload.
 * - Assert that the gateway rejects with a descriptive error.
 *
 * Case:
 * - AC-04 — success=false (logical failure).
 */
describe('AC04 — ListEntriesGateway rejects when success=false with 200 OK', () => {
    let ctx: GatewayTestCtx;

    beforeEach(() => {
        ctx = createGateway();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('throws when API responds with success=false despite 200 status', async () => {
        // prettier-ignore
        const request  = makeRequest();
        const response = makeResponse(request); // success:false

        mockJsonOnce(ctx.fetchMock, 200, response);

        const fn = ctx.gateway.list(request);
        const message = 'Malformed response for GET /api/entries';

        await expect(fn).rejects.toThrowError(message);
    });
});
