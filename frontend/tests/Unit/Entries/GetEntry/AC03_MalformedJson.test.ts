import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {createGateway, mockJsonOnce, type GatewayTestCtx} from './BaseGetEntryGatewayTest';
import {ac03MalformedJson as makeRequest} from '@tests/helpers/http/requests/entries/GetEntryRequestFactory';
import {ac03MalformedJson as makeResponse} from '@tests/helpers/http/responses/entries/GetEntryResponseFactory';

/**
 * UC-3: Get Entry (Frontend, Gateway)
 *
 * Purpose:
 * Verify that GetEntryGateway rejects when API responds with a malformed JSON body,
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
describe('AC03 — GetEntryGateway rejects on malformed JSON (missing data)', () => {
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
        const fn = ctx.gw.get(request);
        const message = 'Malformed response for GET /api/entries/:id';

        // Assert
        await expect(fn).rejects.toThrowError(message);
    });
});
