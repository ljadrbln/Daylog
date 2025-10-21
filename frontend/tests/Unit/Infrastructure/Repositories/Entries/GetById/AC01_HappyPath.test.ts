import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {
    createRepository,
    mockJsonOnce,
    type RepositoryTestCtx
} from '@tests/Unit/Infrastructure/Repositories/Entries/BaseEntriesRepositoryTest';

import {ac01HappyPath as makeRequest} from '@tests/helpers/http/requests/entries/GetEntryRequestFactory';
import {ac01HappyPath as makeResponse} from '@tests/helpers/http/responses/entries/GetEntryResponseFactory';
import {ensureSuccess} from '@tests/helpers/asserts';

/**
 * UC-3: Get Entry (Frontend, Repository)
 *
 * Purpose:
 * Verify that EntryRepository.findById resolves with Entry when API responds
 * 200 + { success:true, data: Entry }.
 */
describe('AC01 — EntryRepository.findById returns entry (mocked)', () => {
    let ctx: RepositoryTestCtx;

    beforeEach(() => {
        ctx = createRepository();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('returns entry when called with valid id', async () => {
        // Arrange
        // prettier-ignore
        const request  = makeRequest();
        const response = makeResponse(request);

        // Ensure test factory returned the success-branch payload.
        const okResponse = ensureSuccess(response);

        // Act
        mockJsonOnce(ctx.fetchMock, 200, response);
        const entry = await ctx.repo.findById(request.id);

        // Assert
        expect(entry.id).toBe(request.id);
        expect(entry.title).toBe(okResponse.data.title);
        expect(entry.body).toBe(okResponse.data.body);
        expect(entry.date).toBe(okResponse.data.date);
        expect(entry.createdAt <= entry.updatedAt).toBe(true);
    });
});
