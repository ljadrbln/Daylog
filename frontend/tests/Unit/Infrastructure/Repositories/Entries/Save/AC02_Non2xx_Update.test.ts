/**
 * @covers EntryRepository.save
 *
 * Purpose:
 * Validate repository behavior for UC-5.
 *
 * Mechanics:
 * - Mock HTTP responses and assert repository invariants.
 *
 * Cases:
 * - AC02 Non-2xx
 */

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
