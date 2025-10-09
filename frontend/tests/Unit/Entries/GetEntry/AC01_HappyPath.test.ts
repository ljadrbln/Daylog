import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {createGateway, mockJsonOnce, type GatewayTestCtx} from './BaseGetEntryGatewayTest';
import {ac01HappyPath as makeRequest} from '@tests/helpers/http/requests/entries/GetEntryRequestFactory';
import {ac01HappyPath as makeResponse} from '@tests/helpers/http/responses/entries/GetEntryResponseFactory';

/**
 * UC-3: Get Entry (Frontend, Gateway)
 *
 * Purpose:
 * Verify that GetEntryGateway resolves with Entry when API responds
 * 200 + { success: true, data: Entry }.
 *
 * Mechanics:
 * - Build a valid request via request factory (no literals at the call site).
 * - Build a matching response via response factory from the same request.
 * - Mock HTTP once with JSON payload and status 200.
 * - Assert the gateway returns Entry consistent with response.data.
 *
 * Cases covered:
 * - AC-01 — Happy path with well-formed JSON and success=true.
 */
describe('AC01 — GetEntryGateway returns entry (mocked)', () => {
    let ctx: GatewayTestCtx;

    beforeEach(() => {
        ctx = createGateway();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('returns entry when called with valid id', async () => {
        // Arrange
        // prettier-ignore
        const request  = makeRequest();
        const response = makeResponse(request);

        mockJsonOnce(ctx.fetchMock, 200, response);

        // Act
        const entry = await ctx.gw.get(request);

        // Assert
        expect(entry.id).toBe(request.id);
        expect(entry.title).toBe(response.data.title);
        expect(entry.body).toBe(response.data.body);
        expect(entry.date).toBe(response.data.date);
        expect(entry.createdAt <= entry.updatedAt).toBe(true);
    });
});
