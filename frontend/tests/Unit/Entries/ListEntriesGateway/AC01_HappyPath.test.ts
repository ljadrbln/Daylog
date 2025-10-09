import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {createGateway, mockJsonOnce, type GatewayTestCtx} from './BaseListEntriesGatewayTest';
import {ac01HappyPath as makeRequest} from '@tests/helpers/http/requests/entries/ListEntriesRequestFactory';
import {ac01HappyPath as makeResponse} from '@tests/helpers/http/responses/entries/ListEntriesResponseFactory';
import {ensureSuccess} from '@tests/helpers/asserts';

/**
 * UC-2: List Entries (Frontend, Gateway)
 *
 * Purpose:
 * Verify that ListEntriesGateway resolves with { items[], page, perPage, total, pagesCount }
 * when API responds 200 + { success:true, data:{...} }.
 *
 * Mechanics:
 * - Build a valid query request via request factory (no literals at the call site).
 * - Build a matching success response via response factory from the same request.
 * - Mock HTTP once with JSON payload and status 200.
 * - Assert the gateway returns data consistent with the response.
 *
 * Cases covered:
 * - AC-01 — Happy path with well-formed JSON and success=true.
 */
describe('AC01 — ListEntriesGateway returns items with pagination (mocked)', () => {
    let ctx: GatewayTestCtx;

    beforeEach(() => {
        ctx = createGateway();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('returns items[] and pagination fields on success=true', async () => {
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
        const data = await ctx.gateway.list(request);

        // Assert — pagination scalars
        expect(data.page).toBe(okResponse.data.page);
        expect(data.perPage).toBe(okResponse.data.perPage);
        expect(data.total).toBe(okResponse.data.total);
        expect(data.pagesCount).toBe(okResponse.data.pagesCount);

        // Assert — items[]
        expect(Array.isArray(data.items)).toBe(true);
        expect(data.items.length).toBe(okResponse.data.items.length);

        if (data.items.length > 0) {
            const first = data.items[0];
            const firstRef = okResponse.data.items[0];

            expect(first.id).toBe(firstRef.id);
            expect(first.title).toBe(firstRef.title);
            expect(first.body).toBe(firstRef.body);
            expect(first.date).toBe(firstRef.date);

            expect(first.createdAt <= first.updatedAt).toBe(true);
        }
    });
});
