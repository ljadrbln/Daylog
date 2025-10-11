// tests/Unit/Entries/UpdateEntry/AC03_MalformedJson.test.ts
import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {createGateway, mockJsonOnce, type GatewayTestCtx} from './BaseUpdateEntryGatewayTest';
import {ac03MalformedJson as makeRequest} from '@tests/helpers/http/requests/entries/UpdateEntryRequestFactory';
import {ac03MalformedJson as makeResponse} from '@tests/helpers/http/responses/entries/UpdateEntryResponseFactory';

/**
 * UC-5: Update Entry (Frontend, Gateway)
 *
 * Purpose:
 * Verify that UpdateEntryGateway rejects when backend sends success=true
 * but the JSON payload is malformed (e.g., missing `data`).
 *
 * Mechanics:
 * - Build a valid request via factory (no literals).
 * - Build a malformed success payload via response factory (no `data`).
 * - Mock fetch with 200 and assert rejection (message indicates malformed).
 *
 * Case:
 * - AC-03 — Malformed JSON on success=true branch.
 */
describe('AC03 — UpdateEntryGateway rejects on malformed JSON (success=true, no data)', () => {
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
        const response = makeResponse(request); // success:true, but no `data`

        // Act
        mockJsonOnce(ctx.fetchMock, 200, response);
        const fn = ctx.gateway.update(request);

        // Assert
        const message = 'Malformed response for PUT /api/entries/:id';
        await expect(fn).rejects.toThrowError(message);
    });
});
