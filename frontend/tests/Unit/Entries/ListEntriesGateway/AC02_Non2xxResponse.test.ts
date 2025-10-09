import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {createGateway, mockJsonOnce, type GatewayTestCtx} from './BaseListEntriesGatewayTest';
import {ac02Non2xxResponse as makeRequest} from '@tests/helpers/http/requests/entries/ListEntriesRequestFactory';
import {
    makeBadRequest,
    makeInternalError
} from '@tests/helpers/http/responses/common/Non2xxResponseFactory';

/**
 * UC-2: List Entries (Frontend, Gateway)
 *
 * Purpose:
 * Verify that ListEntriesGateway rejects on generic non-2xx HTTP responses.
 *
 * Mechanics:
 * - Build a valid request via factory (no literals).
 * - Use common non-2xx response factories (no domain codes asserted here).
 * - Mock fetch with 400/500 and assert rejection.
 *
 * Cases:
 * - AC-02a — 400 Bad Request → rejects.
 * - AC-02b — 500 Internal Server Error → rejects.
 */
describe('AC02 — ListEntriesGateway throws on non-2xx response (generic)', () => {
    let ctx: GatewayTestCtx;

    beforeEach(() => {
        ctx = createGateway();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('throws when API responds with 400 Bad Request', async () => {
        const request = makeRequest();
        const response = makeBadRequest();

        mockJsonOnce(ctx.fetchMock, 400, response);

        const fn = ctx.gateway.list(request);
        const message = /400|bad request/i;

        await expect(fn).rejects.toThrowError(message);
    });

    it('throws when API responds with 500 Internal Server Error', async () => {
        const request = makeRequest();
        const response = makeInternalError();

        mockJsonOnce(ctx.fetchMock, 500, response);

        const fn = ctx.gateway.list(request);
        const message = /500|internal/i;

        await expect(fn).rejects.toThrowError(message);
    });
});
