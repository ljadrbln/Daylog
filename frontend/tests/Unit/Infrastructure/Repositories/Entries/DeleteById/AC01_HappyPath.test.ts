import {describe, it, beforeEach, afterEach} from 'vitest';
import {
    createRepository,
    mockJsonOnce,
    type RepositoryTestCtx
} from '@tests/Unit/Infrastructure/Repositories/Entries/BaseEntriesRepositoryTest';
import {ac01HappyPath as makeRequest} from '@tests/helpers/http/requests/entries/DeleteEntryRequestFactory';
import {ac01HappyPath as makeResponse} from '@tests/helpers/http/responses/entries/DeleteEntryResponseFactory';

/** UC-4 Delete Entry (Repository): 200 + {success:true} -> resolves void. */
describe('AC01 — EntryRepository.deleteById resolves on success', () => {
    let ctx: RepositoryTestCtx;

    beforeEach(() => {
        ctx = createRepository();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('resolves when API returns success=true', async () => {
        // Arrange
        const req = makeRequest();
        const res = makeResponse(req);
        mockJsonOnce(ctx.fetchMock, 200, res);

        // Act
        await ctx.repo.deleteById(req.id);
    });
});
