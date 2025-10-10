import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {createGateway, mockJsonOnce, type GatewayTestCtx} from './BaseDeleteEntryGatewayTest';
import {ac01HappyPath as makeRequest} from '@tests/helpers/http/requests/entries/DeleteEntryRequestFactory';
import {ac01HappyPath as makeResponse} from '@tests/helpers/http/responses/entries/DeleteEntryResponseFactory';
import {ensureSuccess} from '@tests/helpers/asserts';

/**
 * UC-4: Delete Entry (Frontend, Gateway)
 *
 * Purpose:
 * Verify that DeleteEntryGateway resolves with Entry when API responds
 * 200 + { success: true, data: Entry }.
 *
 * Mechanics:
 * - Build a valid request via request factory (no literals).
 * - Build a matching success response via response factory from the same request.
 * - Mock HTTP once with JSON payload and status 200.
 * - Call gateway.delete(request) and assert returned Entry matches response.data.
 *
 * Cases covered:
 * - AC-01 — Happy path with well-formed JSON and success=true.
 */
describe('AC01 — DeleteEntryGateway returns Entry (mocked)', () => {
    let ctx: GatewayTestCtx;

    beforeEach(() => {
        ctx = createGateway();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('returns Entry when called with valid id', async () => {
        // Arrange
        // prettier-ignore
        const request  = makeRequest();
        const response = makeResponse(request);

        // Narrow union to the success branch for safe access to data.*
        const ok = ensureSuccess(response);

        // Act
        mockJsonOnce(ctx.fetchMock, 200, response);
        const entry = await ctx.gateway.delete(request);

        // Assert
        expect(entry.id).toBe(request.id);
        expect(entry.title).toBe(ok.data.title);
        expect(entry.body).toBe(ok.data.body);
        expect(entry.date).toBe(ok.data.date);

        // createdAt must be <= updatedAt
        expect(entry.createdAt <= entry.updatedAt).toBe(true);
    });
});
