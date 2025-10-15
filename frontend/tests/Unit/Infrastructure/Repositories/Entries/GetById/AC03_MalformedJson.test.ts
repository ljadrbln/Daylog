import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {
    createRepository,
    mockJsonOnce,
    type RepositoryTestCtx
} from '@tests/Unit/Infrastructure/Repositories/Entries/BaseEntriesRepositoryTest';
import {ac03MalformedJson as makeRequest} from '@tests/helpers/http/requests/entries/GetEntryRequestFactory';
import {ac03MalformedJson as makeResponse} from '@tests/helpers/http/responses/entries/GetEntryResponseFactory';

/**
 * UC-3: Get Entry (Repository)
 *
 * Purpose:
 * Verify that EntryRepository.findById rejects when API responds
 * with malformed JSON: success=true but missing required `data`.
 *
 * @covers EntryRepository
 */
describe('AC03 — EntryRepository.findById rejects on malformed JSON (missing data)', () => {
    let ctx: RepositoryTestCtx;

    beforeEach(() => {
        ctx = createRepository();
    });
    afterEach(() => {
        ctx.cleanup();
    });

    it('throws when success=true but `data` is missing', async () => {
        // Arrange
        const request = makeRequest();
        const response = makeResponse(request); // success:true, no data
        mockJsonOnce(ctx.fetchMock, 200, response);

        // Act
        const fn = ctx.repo.findById(request.id);
        const message = 'Malformed response for GET /api/entries/:id';

        // Assert
        await expect(fn).rejects.toThrowError(message);
    });
});
