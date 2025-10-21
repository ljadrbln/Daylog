// frontend/tests/Unit/Entries/AddEntry/Http/AC03_MalformedJson.test.ts
import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {createGateway, mockJsonOnce, type GatewayTestCtx} from './BaseAddEntryGatewayTest';
import {ac03MalformedJson as makeRequest} from '@tests/helpers/http/requests/entries/AddEntryRequestFactory';
import {malformed as makeResponse} from '@tests/helpers/http/responses/entries/AddEntryResponseFactory';

/**
 * UC-1: Add Entry (Frontend, Gateway)
 *
 * Purpose:
 * Verify that HttpAddEntryGateway rejects when API responds with malformed JSON payload.
 *
 * Mechanics:
 * - Build a valid request via factory (no literals).
 * - Mock fetch to return plain text that cannot be parsed as JSON.
 * - Expect the gateway to reject with an error mentioning invalid/malformed JSON.
 *
 * Cases covered:
 * - AC-03 — Malformed JSON response causes rejection.
 */
describe('AC03 — AddEntryGateway throws on malformed JSON', () => {
    let ctx: GatewayTestCtx;

    beforeEach(() => {
        ctx = createGateway();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('throws when API responds with malformed JSON', async () => {
        // Arrange
        // prettier-ignore
        const request  = makeRequest();
        const response = makeResponse(request);

        // Act
        mockJsonOnce(ctx.fetchMock, 200, response);
        const fn = ctx.gateway.add(request);

        // Assert
        const message = 'Malformed response for POST /api/entries';
        await expect(fn).rejects.toThrow(message);
    });
});
