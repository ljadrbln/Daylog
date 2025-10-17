// tests/Unit/Entries/UpdateEntry/AC04_SuccessFalse.test.ts
import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {createGateway, mockJsonOnce, type GatewayTestCtx} from './BaseUpdateEntryGatewayTest';
import {ac04SuccessFalse as makeRequest} from '@tests/helpers/http/requests/entries/UpdateEntryRequestFactory';
import {successFalse as makeResponse} from '@tests/helpers/http/responses/entries/UpdateEntryResponseFactory';

/**
 * UC-5: Update Entry (Frontend, Gateway)
 *
 * Purpose:
 * Verify that UpdateEntryGateway rejects when backend returns
 * { success:false, status:200, code:... } for logical failure.
 *
 * Mechanics:
 * - Build valid request via factory.
 * - Build success=false payload via response factory.
 * - Mock fetch with HTTP 200 and assert rejection (message indicates malformed).
 *
 * Case:
 * - AC-04 — success=false (200) must be rejected by transport checks.
 */
describe('AC04 — UpdateEntryGateway rejects when success=false with HTTP 200', () => {
    let ctx: GatewayTestCtx;

    beforeEach(() => {
        ctx = createGateway();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('throws when backend responds {success:false, status:200}', async () => {
        // Arrange
        // prettier-ignore
        const request  = makeRequest();
        const response = makeResponse(request); // success:false, 200

        // Act
        mockJsonOnce(ctx.fetchMock, 200, response);
        const fn = ctx.gateway.update(request);

        // Assert
        const message = 'Malformed response for PUT /api/entries/:id';
        await expect(fn).rejects.toThrowError(message);
    });
});
