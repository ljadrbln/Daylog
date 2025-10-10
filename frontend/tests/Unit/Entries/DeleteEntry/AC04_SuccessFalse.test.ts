import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {createGateway, mockJsonOnce, type GatewayTestCtx} from './BaseDeleteEntryGatewayTest';
import {ac04SuccessFalse as makeRequest} from '@tests/helpers/http/requests/entries/DeleteEntryRequestFactory';
import {ac04SuccessFalse as makeResponse} from '@tests/helpers/http/responses/entries/DeleteEntryResponseFactory';

/**
 * UC-4: Delete Entry (Frontend, Gateway)
 *
 * Purpose:
 * Contract guard: reject when API responds with { success:false } while HTTP status is 200.
 * Backend should not send logical failures with 200; if it happens, gateway must treat it as malformed.
 *
 * Mechanics:
 * - Build a valid request via factory (no literals).
 * - Build a mocked response with success=false via response factory.
 * - Mock HTTP once with status 200 and the error-like payload.
 * - Assert that the gateway rejects with a descriptive error.
 *
 * Cases covered:
 * - AC-04 — Contract guard: success=false with 200 → reject as malformed.
 */
describe('AC04 — DeleteEntryGateway rejects when success=false with 200 OK (contract guard)', () => {
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
        const response = makeResponse(request); // success:false, 200

        mockJsonOnce(ctx.fetchMock, 200, response);

        // Act
        const fn = ctx.gateway.delete(request);
        const message = 'Malformed response for DELETE /api/entries/:id';

        // Assert
        await expect(fn).rejects.toThrowError(message);
    });
});
