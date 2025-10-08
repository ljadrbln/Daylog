import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {createGateway, mockJsonOnce, type GatewayTestCtx} from './BaseAddEntryGatewayTest';
import {ac04SuccessFalse as makeRequest} from '@tests/helpers/http/requests/entries/AddEntryRequestFactory';
import {ac04SuccessFalse as makeResponse} from '@tests/helpers/http/responses/entries/AddEntryResponseFactory';

/**
 * UC-1: Add Entry (Frontend, Gateway)
 *
 * Purpose:
 * Verify that HttpAddEntryGateway rejects when API responds with success=false,
 * even if HTTP status is 200.
 *
 * Mechanics:
 * - Build a valid request via factory.
 * - Mock fetch with JSON { success:false, status:200, code:"ANY_CODE" }.
 * - Expect the gateway to throw transport-level error.
 *
 * Cases covered:
 * - AC-04 — success=false + 200 → rejects.
 */
describe('AC04 — AddEntryGateway throws when success=false (even with 200)', () => {
    let ctx: GatewayTestCtx;

    beforeEach(() => {
        ctx = createGateway();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('throws when API returns success=false', async () => {
        // Arrange
        // prettier-ignore
        const request  = makeRequest();
        const response = makeResponse(request);

        mockJsonOnce(ctx.fetchMock, 200, response);

        // Act
        const fn = ctx.gw.add(request);
        const message = 'Malformed response for POST /api/entries';

        // Assert
        await expect(fn).rejects.toThrow(message);
    });
});
