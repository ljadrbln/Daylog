import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {
    createRepository,
    mockJsonOnce,
    type RepositoryTestCtx
} from '@tests/Unit/Infrastructure/Repositories/Entries/BaseEntriesRepositoryTest';

import {ensureSuccess} from '@tests/helpers/asserts';

import type {Entry} from '@src/Domain/Models/Entries/Entry';
import {EntryFactory} from '@tests/helpers/domain/entries/EntryFactory';
import {happyPath as makeResponse} from '@tests/helpers/http/responses/entries/AddEntryResponseFactory';

/**
 * UC-1: Save Entry (create) — Repository happy path.
 *
 * Purpose:
 * Verify that EntryRepository.save correctly resolves with a fully populated Entry
 * when the backend responds with HTTP 200 and a valid UseCaseResponse envelope:
 * { success: true, data: Entry } for POST /api/entries.
 *
 * Mechanics:
 * - Producing a valid Entry body.
 * - Stub the backend response with AddEntryResponseFactory.happyPath(), which returns
 *   a consistent success envelope containing the same Entry data plus backend-generated fields (id, timestamps).
 * - Invoke repo.save(entry) — since entry.id === '', the repository issues POST /api/entries.
 * - Await the Promise and ensure that:
 *   1) The resolved Entry deeply equals the data returned by the backend.
 *   2) createdAt ≤ updatedAt, preserving BR-2 (monotonic timestamps).
 *
 * Acceptance mapping:
 * - UC-1 / AC-1 — successful creation of a new entry with correct timestamps.
 *
 * @covers EntriesRepository.save
 */
describe('AC01 — EntryRepository.save returns entry (mocked)', () => {
    let ctx: RepositoryTestCtx;

    beforeEach(() => {
        ctx = createRepository();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('returns saved entry when backend responds with success=true', async () => {
        // Arrange
        const entry = EntryFactory.makeForCreate();
        const res = makeResponse(entry);
        mockJsonOnce(ctx.fetchMock, 200, res);

        // Act
        const okResponse = ensureSuccess(res);
        const expected: Entry = okResponse.data;
        const result = await ctx.repo.save(entry);

        // Assert
        expect(result).toEqual(expected);
        expect(result.createdAt <= result.updatedAt).toBe(true);
    });
});
