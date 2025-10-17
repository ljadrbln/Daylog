import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {
    createRepository,
    mockJsonOnce,
    type RepositoryTestCtx
} from '@tests/Unit/Infrastructure/Repositories/Entries/BaseEntriesRepositoryTest';
import {EntryFactory} from '@tests/helpers/domain/entries/EntryFactory';
import {successFalse} from '@tests/helpers/http/responses/entries/AddEntryResponseFactory';

/**
 * UC-5: Save (update) — AC04 success=false → reject.
 * Mechanics: PUT /api/entries/{id} with Entry (id!=''), enqueue success=false.
 * @covers EntriesRepository.save
 */
describe('AC04 — save(update) rejects on success=false', () => {
    let ctx: RepositoryTestCtx;

    beforeEach(() => {
        ctx = createRepository();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('throws when envelope has success=false', async () => {
        // Arrange
        const entry = EntryFactory.makeForUpdate();
        mockJsonOnce(ctx.fetchMock, 200, successFalse());

        // Act
        const act = ctx.repo.save(entry);

        // Assert
        await expect(act).rejects.toThrow(/success=false|malformed|error/i);
    });
});
