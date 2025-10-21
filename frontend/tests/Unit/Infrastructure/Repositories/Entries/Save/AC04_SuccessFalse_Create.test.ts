import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {
    createRepository,
    mockJsonOnce,
    type RepositoryTestCtx
} from '@tests/Unit/Infrastructure/Repositories/Entries/BaseEntriesRepositoryTest';
import {EntryFactory} from '@tests/helpers/domain/entries/EntryFactory';
import {successFalse} from '@tests/helpers/http/responses/entries/AddEntryResponseFactory';

/**
 * UC-1: Save (create) — AC04 success=false → reject.
 * Mechanics: POST /api/entries with Entry (id=''), enqueue success=false.
 * @covers EntriesRepository.save
 */
describe('AC04 — save(create) rejects on success=false', () => {
    let ctx: RepositoryTestCtx;

    beforeEach(() => {
        ctx = createRepository();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('throws when envelope has success=false', async () => {
        // Arrange
        const entry = EntryFactory.makeForCreate();
        mockJsonOnce(ctx.fetchMock, 200, successFalse());

        // Act
        const act = ctx.repo.save(entry);

        // Assert
        await expect(act).rejects.toThrow(/success=false|malformed|error/i);
    });
});
