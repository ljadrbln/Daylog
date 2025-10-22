import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {
    createRepository,
    mockJsonOnce,
    type RepositoryTestCtx
} from '@tests/Unit/Infrastructure/Repositories/Entries/BaseEntriesRepositoryTest';
import {ac02Non2xxResponse as makeRequest} from '@tests/helpers/http/requests/entries/GetEntryRequestFactory';
 import {notFound} from '@tests/helpers/http/responses/common/Non2xxResponseFactory';

/**
 * @covers EntryRepository.findById
 *
 * Purpose:
 * Validate UC-3 behavior: 404 Not Found MUST be mapped to `null`.
 *
 * Mechanics:
 * - Build request via factory.
 * - Enqueue JSON with 404 {success:false,status:404,code:'ENTRY_NOT_FOUND'}.
 * - Call repo.findById and assert it resolves to null (no exception).
 *
 * Cases:
 * - AC02 Not Found (404) ⇒ returns null
 */
describe('AC02 — EntryRepository.findById returns null on 404 Not Found', () => {
    let ctx: RepositoryTestCtx;

    beforeEach(() => {
        ctx = createRepository();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('returns null when API responds with 404 Not Found', async () => {
        // Arrange
        const request = makeRequest();

        const response = notFound();
        mockJsonOnce(ctx.fetchMock, 404, response);

        // Act
        const result = await ctx.repo.findById(request.id);

        // Assert
        expect(result).toBeNull();
    });
});
