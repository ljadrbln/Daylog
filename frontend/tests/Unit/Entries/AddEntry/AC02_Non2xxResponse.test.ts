// frontend/tests/Unit/Entries/AddEntry/Http/AC02_Non2xxResponse.test.ts
import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {createGateway, mockJsonOnce, type GatewayTestCtx} from './BaseAddEntryGatewayTest';
import {ac02Non2xxResponse as makeRequest} from '@tests/helpers/http/requests/entries/AddEntryRequestFactory';
import {
    badRequest,
    internalServerError
} from '@tests/helpers/http/responses/common/Non2xxResponseFactory';

/**
 * UC-1: Add Entry (Frontend, Gateway)
 *
 * Purpose:
 * Verify that HttpAddEntryGateway rejects on generic non-2xx HTTP responses.
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
describe('AC02 — AddEntryGateway throws on non-2xx response (generic)', () => {
    let ctx: GatewayTestCtx;

    beforeEach(() => {
        ctx = createGateway();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('throws when API responds with 400 Bad Request', async () => {
        // Arrange
        // prettier-ignore
        const request  = makeRequest();
        const response = badRequest();

        // Act
        mockJsonOnce(ctx.fetchMock, 400, response);
        const fn = ctx.gateway.add(request);

        // Assert
        const message = /400|bad request/i;
        await expect(fn).rejects.toThrowError(message);
    });

    it('throws when API responds with 500 Internal Server Error', async () => {
        // Arrange
        // prettier-ignore
        const request  = makeRequest();
        const response = internalServerError();

        // Act
        mockJsonOnce(ctx.fetchMock, 500, response);
        const fn = ctx.gateway.add(request);

        // Assert
        const message = /500|internal/i;
        await expect(fn).rejects.toThrowError(message);
    });
});
