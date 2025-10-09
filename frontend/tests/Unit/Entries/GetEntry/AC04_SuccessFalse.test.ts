import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {createGateway, mockJsonOnce, type GatewayTestCtx} from './BaseGetEntryGatewayTest';
import {ac04SuccessFalse as makeRequest} from '@tests/helpers/http/requests/entries/GetEntryRequestFactory';
import {ac04SuccessFalse as makeResponse} from '@tests/helpers/http/responses/entries/GetEntryResponseFactory';

/**
 * UC-3: Get Entry (Frontend, Gateway)
 *
 * Purpose:
 * Verify that GetEntryGateway rejects when API responds with { success:false }
 * even though HTTP status is 200.
 *
 * Mechanics:
 * - Build a valid request via factory (no literals).
 * - Build a mocked response with success=false via response factory.
 * - Mock HTTP once with status 200 and the error-like payload.
 * - Assert that the gateway rejects with a descriptive error.
 *
 * Cases covered:
 * - AC-04 — success=false (logical failure).
 */
describe('AC04 — GetEntryGateway rejects when success=false with 200 OK', () => {
    let ctx: GatewayTestCtx;

    beforeEach(() => {
        ctx = createGateway();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('throws when API responds with success=false despite 200 status', async () => {
        // Arrange
        // prettier-ignore
        const request  = makeRequest();
        const response = makeResponse(request); // success:false

        mockJsonOnce(ctx.fetchMock, 200, response);

        // Act
        const fn = ctx.gateway.get(request);
        const message = 'Malformed response for GET /api/entries/:id';

        // Assert
        await expect(fn).rejects.toThrowError(message);
    });
});
