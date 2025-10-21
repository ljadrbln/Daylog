import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {createGateway, mockJsonOnce, type GatewayTestCtx} from './BaseAddEntryGatewayTest';
import {ac01HappyPath as makeRequest} from '@tests/helpers/http/requests/entries/AddEntryRequestFactory';
import {happyPath as makeResponse} from '@tests/helpers/http/responses/entries/AddEntryResponseFactory';
import {ensureSuccess} from '@tests/helpers/asserts';

/**
 * UC-1: Add Entry (Frontend, Gateway)
 *
 * Purpose:
 * Verify that HttpAddEntryGateway resolves with Entry when API responds
 * 200 + { success: true, data.item }.
 *
 * Mechanics:
 * - Build a valid request via request factory (no literals in the call site).
 * - Build a matching response via response factory from the same request.
 * - Mock HTTP once with JSON payload and status 200.
 * - Assert the gateway returns Entry consistent with response.item.
 *
 * Cases covered:
 * - AC-01 — Happy path with well-formed JSON and success=true.
 */
describe('AC01 — AddEntryGateway returns entry (mocked)', () => {
    let ctx: GatewayTestCtx;

    beforeEach(() => {
        ctx = createGateway();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('returns entry when called with valid payload', async () => {
        // Arrange
        // prettier-ignore
        const request  = makeRequest();
        const response = makeResponse(request);

        // Ensure test factory returned the success-branch payload.
        // This guard narrows the union type AddEntryResponse so that
        // TypeScript recognizes `data` as defined in the success case.
        const okResponse = ensureSuccess(response);

        // Act
        mockJsonOnce(ctx.fetchMock, 200, okResponse);
        const entry = await ctx.gateway.add(request);

        // Assert
        expect(entry.title).toBe(okResponse.data.title);
        expect(entry.body).toBe(okResponse.data.body);
        expect(entry.date).toBe(okResponse.data.date);

        expect(entry.createdAt <= entry.updatedAt).toBe(true);
    });
});
