import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {
    createRepository,
    mockJsonOnce,
    type RepositoryTestCtx
} from '@tests/Unit/Infrastructure/Repositories/Entries/BaseEntriesRepositoryTest';
import {EntryFactory} from '@tests/helpers/domain/entries/EntryFactory';
import {
    badRequest,
    internalServerError
} from '@tests/helpers/http/responses/common/Non2xxResponseFactory';

/**
 * UC-5: Save (update) — AC02 non-2xx → reject.
 * Mechanics: PATCH /api/entries/{id} with Entry (id!=''), enqueue 400/500, expect throw.
 * @covers EntriesRepository.save
 */
describe('AC02 — save(update) rejects on non-2xx', () => {
    let ctx: RepositoryTestCtx;

    beforeEach(() => {
        ctx = createRepository();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('throws on 400 Bad Request', async () => {
        // Arrange
        const entry = EntryFactory.makeForUpdate();
        mockJsonOnce(ctx.fetchMock, 400, badRequest());

        // Act
        const act = ctx.repo.save(entry);

        // Assert
        await expect(act).rejects.toThrow(/400|bad request/i);
    });

    it('throws on 500 Internal Server Error', async () => {
        // Arrange
        const entry = EntryFactory.makeForUpdate();
        mockJsonOnce(ctx.fetchMock, 500, internalServerError());

        // Act
        const act = ctx.repo.save(entry);

        // Assert
        await expect(act).rejects.toThrow(/500|internal/i);
    });
});
