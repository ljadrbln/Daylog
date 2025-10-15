import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {
    createRepository,
    mockJsonOnce,
    type RepositoryTestCtx
} from '@tests/Unit/Infrastructure/Repositories/Entries/BaseEntriesRepositoryTest';
import {ac02Non2xxResponse as makeRequest} from '@tests/helpers/http/requests/entries/DeleteEntryRequestFactory';
import {
    makeBadRequest,
    makeInternalError
} from '@tests/helpers/http/responses/common/Non2xxResponseFactory';

/** UC-4 Delete Entry (Repository): non-2xx -> rejects. */
describe('AC02 — EntryRepository.deleteById rejects on non-2xx', () => {
    let ctx: RepositoryTestCtx;

    beforeEach(() => {
        ctx = createRepository();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('400 Bad Request', async () => {
        // Arrange
        const req = makeRequest();
        mockJsonOnce(ctx.fetchMock, 400, makeBadRequest());

        // Act
        const fn = ctx.repo.deleteById(req.id);

        // Assert
        await expect(fn).rejects.toThrow(/400|bad request/i);
    });

    it('500 Internal Server Error', async () => {
        // Arrange
        const req = makeRequest();
        mockJsonOnce(ctx.fetchMock, 500, makeInternalError());

        // Act
        const fn = ctx.repo.deleteById(req.id)

        // Assert
        await expect(fn).rejects.toThrow(/500|internal/i);
    });
});
