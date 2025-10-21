import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {createGateway, mockJsonOnce, type GatewayTestCtx} from './BaseUpdateEntryGatewayTest';
import {ac01HappyPath as makeRequest} from '@tests/helpers/http/requests/entries/UpdateEntryRequestFactory';
import {happyPath as makeResponse} from '@tests/helpers/http/responses/entries/UpdateEntryResponseFactory';
import {ensureSuccess} from '@tests/helpers/asserts';

/**
 * UC-5: Update Entry (Frontend, Gateway)
 *
 * Purpose:
 * Verify that UpdateEntryGateway resolves with Entry when API responds
 * 200 + { success: true, data }.
 *
 * Mechanics:
 * - Build request via factory (no literals at call site).
 * - Build matching response via response factory from the same request.
 * - Mock HTTP once with JSON payload and status 200.
 * - Assert gateway returns Entry consistent with response.data.
 *
 * Case:
 * - AC-01 — Happy path with success=true and well-formed JSON.
 */
describe('AC01 — UpdateEntryGateway returns updated entry (mocked)', () => {
    let ctx: GatewayTestCtx;

    beforeEach(() => {
        ctx = createGateway();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('returns updated entry when called with valid payload', async () => {
        // Arrange
        // prettier-ignore
        const request  = makeRequest();
        const response = makeResponse(request);

        // Guard: ensure success branch with `data` is selected.
        const okResponse = ensureSuccess(response);

        // Act
        mockJsonOnce(ctx.fetchMock, 200, okResponse);
        const entry = await ctx.gateway.update(request);

        // Assert
        expect(entry.id).toBe(okResponse.data.id);
        expect(entry.title).toBe(okResponse.data.title);
        expect(entry.body).toBe(okResponse.data.body);
        expect(entry.date).toBe(okResponse.data.date);
        expect(typeof entry.createdAt).toBe('string');
        expect(typeof entry.updatedAt).toBe('string');
        expect(entry.createdAt <= entry.updatedAt).toBe(true);
    });
});
