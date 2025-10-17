import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {
    createRepository,
    mockJsonOnce,
    type RepositoryTestCtx
} from '@tests/Unit/Infrastructure/Repositories/Entries/BaseEntriesRepositoryTest';

import {ensureSuccess} from '@tests/helpers/asserts';

import type {Entry} from '@src/Domain/Entries/Entry';
import {EntryFactory} from '@tests/helpers/domain/entries/EntryFactory';
import {happyPath as makeResponse} from '@tests/helpers/http/responses/entries/UpdateEntryResponseFactory';

/**
 * UC-5: Save Entry (update) — Repository happy path.
 *
 * Purpose:
 * - Ensure EntryRepository.save resolves with Entry when backend responds
 *   200 + {success:true, data: Entry} for PATCH /api/entries/:id.
 *
 * Mechanics:
 * - Build request via UpdateEntryRequestFactory.ac01HappyPath().
 * - Enqueue a matching success envelope via UpdateEntryResponseFactory.ac01HappyPath().
 * - Call repo.save(entry) and assert returned Entry equals backend data.
 *
 * @covers EntriesRepository.save
 */
describe('AC01 — EntryRepository.save (update) returns entry (mocked)', () => {
    let ctx: RepositoryTestCtx;

    beforeEach(() => {
        ctx = createRepository();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('returns updated entry when backend responds with success=true', async () => {
        // Arrange
        const entry = EntryFactory.makeForUpdate();
        const res = makeResponse(entry);
        mockJsonOnce(ctx.fetchMock, 200, res);

        // Act
        const okResponse = ensureSuccess(res);
        const expected: Entry = okResponse.data;
        const result = await ctx.repo.save(entry);

        // Assert
        expect(result).toEqual(expected);

        const createdNotAfterUpdated = result.createdAt <= result.updatedAt;
        expect(createdNotAfterUpdated).toBe(true);

        const sameId = result.id === entry.id;
        expect(sameId).toBe(true);
    });
});
