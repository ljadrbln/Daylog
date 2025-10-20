import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {
    createRepository,
    mockJsonOnce,
    type RepositoryTestCtx
} from '@tests/Unit/Infrastructure/Repositories/Entries/BaseEntriesRepositoryTest';

import {ac02Non2xxResponse as makeRequest} from '@tests/helpers/http/requests/entries/DeleteEntryRequestFactory';
import {
    badRequest,
    internalServerError
} from '@tests/helpers/http/responses/common/Non2xxResponseFactory';

/**
 * UC-4: Delete Entry (Repository)
 *
 * Purpose:
 * Verify that EntryRepository.deleteById rejects on generic non-2xx responses.
 * Mechanics:
 * - Build request via factory.
 * - Enqueue JSON with 400/500.
 * - Expect rejection with readable message mentioning status.
 *
 * @covers EntryRepository.deleteById
 */
describe('AC02 — EntryRepository.deleteById throws on non-2xx response (generic)', () => {
    let ctx: RepositoryTestCtx;

    beforeEach(() => {
        ctx = createRepository();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('throws when API responds with 400 Bad Request', async () => {
        // Arrange
        const req = makeRequest();
        const res = badRequest();
        mockJsonOnce(ctx.fetchMock, 400, res);

        // Act
        const fn = ctx.repo.deleteById(req.id);

        // Assert
        await expect(fn).rejects.toThrowError(/400|bad request/i);
    });

    it('throws when API responds with 500 Internal Server Error', async () => {
        // Arrange
        const req = makeRequest();
        const res = internalServerError();
        mockJsonOnce(ctx.fetchMock, 500, res);

        // Act
        const fn = ctx.repo.deleteById(req.id);

        // Assert
        await expect(fn).rejects.toThrowError(/500|internal/i);
    });
});
