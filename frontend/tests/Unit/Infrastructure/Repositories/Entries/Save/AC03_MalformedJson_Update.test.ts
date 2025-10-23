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
 * - AC03 Malformed JSON
 */

import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {
    createRepository,
    mockJsonOnce,
    type RepositoryTestCtx
} from '@tests/Unit/Infrastructure/Repositories/Entries/BaseEntriesRepositoryTest';
import {EntryFactory} from '@tests/helpers/domain/entries/EntryFactory';
import {malformed} from '@tests/helpers/http/responses/entries/AddEntryResponseFactory';

describe('AC03 — save(update) rejects on malformed JSON', () => {
    let ctx: RepositoryTestCtx;

    beforeEach(() => {
        ctx = createRepository();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('throws when envelope is malformed (success=true, data=null)', async () => {
        // Arrange
        const entry = EntryFactory.makeForUpdate();
        mockJsonOnce(ctx.fetchMock, 200, malformed());

        // Act
        const act = ctx.repo.save(entry);

        // Assert
        await expect(act).rejects.toThrow(/malformed/i);
    });
});
