// frontend/tests/Unit/Entries/AddEntry/Http/AC02_Non2xxResponse.test.ts
import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {createGateway, mockJsonOnce, type GatewayTestCtx} from './BaseAddEntryGatewayTest';
import {ac01HappyPath as makeRequest} from '@tests/helpers/http/requests/entries/AddEntryRequestFactory';
import {
    makeBadRequest,
    makeInternalError
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
        const request  = makeRequest();
        const response = makeBadRequest();
        const status   = 400;

        mockJsonOnce(ctx.fetchMock, status, response);

        // Act
        const call = async () => {
            await ctx.gw.add(request);
        };

        // Assert
        await expect(call).rejects.toThrowError(/400|bad request/i);
    });

    it('throws when API responds with 500 Internal Server Error', async () => {
        // Arrange
        const request  = makeRequest();
        const response = makeInternalError();
        const status   = 500;

        mockJsonOnce(ctx.fetchMock, status, response);

        // Act
        const call = async () => {
            await ctx.gw.add(request);
        };

        // Assert
        await expect(call).rejects.toThrowError(/500|internal/i);
    });
});
