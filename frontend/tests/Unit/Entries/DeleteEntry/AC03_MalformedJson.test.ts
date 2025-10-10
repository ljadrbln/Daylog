import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {createGateway, mockJsonOnce, type GatewayTestCtx} from './BaseDeleteEntryGatewayTest';
import {ac03MalformedJson as makeRequest} from '@tests/helpers/http/requests/entries/DeleteEntryRequestFactory';
import {ac03MalformedJson as makeResponse} from '@tests/helpers/http/responses/entries/DeleteEntryResponseFactory';

/**
 * UC-4: Delete Entry (Frontend, Gateway)
 *
 * Purpose:
 * Verify that DeleteEntryGateway rejects when API responds with a malformed JSON body,
 * i.e. success=true but missing required `data` property.
 *
 * Mechanics:
 * - Build a valid request via request factory (no literals at the call site).
 * - Build a malformed success response via response factory (no `data`).
 * - Mock HTTP once with JSON payload and status 200.
 * - Assert the gateway rejects with a Malformed* style error.
 *
 * Cases covered:
 * - AC-03 — Malformed JSON (success=true without `data`).
 */
describe('AC03 — DeleteEntryGateway rejects on malformed JSON (missing data)', () => {
    let ctx: GatewayTestCtx;

    beforeEach(() => {
        ctx = createGateway();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('throws when success=true but `data` is missing', async () => {
        // Arrange
        // prettier-ignore
        const request  = makeRequest();
        const response = makeResponse(request); // success:true, no data

        mockJsonOnce(ctx.fetchMock, 200, response);

        // Act
        const fn = ctx.gateway.delete(request);
        const message = 'Malformed response for DELETE /api/entries/:id';

        // Assert
        await expect(fn).rejects.toThrowError(message);
    });
});
