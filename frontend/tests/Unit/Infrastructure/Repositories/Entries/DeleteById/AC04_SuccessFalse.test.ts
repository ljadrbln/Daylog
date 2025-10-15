import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {
    createRepository,
    mockJsonOnce,
    type RepositoryTestCtx
} from '@tests/Unit/Infrastructure/Repositories/Entries/BaseEntriesRepositoryTest';
import {ac04SuccessFalse as makeRequest} from '@tests/helpers/http/requests/entries/DeleteEntryRequestFactory';
import {ac04SuccessFalse as makeResponse} from '@tests/helpers/http/responses/entries/DeleteEntryResponseFactory';

/** UC-4 Delete Entry (Repository): 200 + {success:false} -> rejects. */
describe('AC04 — EntryRepository.deleteById rejects when success=false with 200', () => {
    let ctx: RepositoryTestCtx;

    beforeEach(() => {
        ctx = createRepository();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('throws on success=false', async () => {
        // Assert
        const req = makeRequest();
        const res = makeResponse(req);
        mockJsonOnce(ctx.fetchMock, 200, res);

        // Act
        const fn = ctx.repo.deleteById(req.id);
        const message = 'Malformed response for DELETE /api/entries/:id';

        // Assert
        await expect(fn).rejects.toThrow(message);
    });
});
