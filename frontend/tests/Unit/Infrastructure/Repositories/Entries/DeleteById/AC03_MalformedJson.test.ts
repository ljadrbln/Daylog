import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {
    createRepository,
    mockJsonOnce,
    type RepositoryTestCtx
} from '@tests/Unit/Infrastructure/Repositories/Entries/BaseEntriesRepositoryTest';
import {ac03MalformedJson as makeRequest} from '@tests/helpers/http/requests/entries/DeleteEntryRequestFactory';
import {ac03MalformedJson as makeResponse} from '@tests/helpers/http/responses/entries/DeleteEntryResponseFactory';

/**
 * UC-4: Delete Entry (Repository)
 *
 * Purpose:
 * Verify that EntryRepository.deleteById rejects when API responds
 * with malformed JSON — success=true but missing required data.
 *
 * @covers EntryRepository
 */
describe('AC03 — EntryRepository.deleteById rejects on malformed JSON (missing data)', () => {
    let ctx: RepositoryTestCtx;

    beforeEach(() => {
        ctx = createRepository();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('throws when success=true but data is missing', async () => {
        // Arrange
        const request = makeRequest();
        const response = makeResponse(request);
        mockJsonOnce(ctx.fetchMock, 200, response);

        // Act
        const fn = ctx.repo.deleteById(request.id);
        const message = 'Malformed response for DELETE /api/entries/:id';

        // Assert
        await expect(fn).rejects.toThrowError(message);
    });
});
