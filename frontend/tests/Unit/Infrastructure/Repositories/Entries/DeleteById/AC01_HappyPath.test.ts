/**
 * @covers EntryRepository.deleteById
 *
 * Purpose:
 * Validate repository behavior for UC-4 (DeleteEntry) on the happy path.
 *
 * Mechanics:
 * - Mock HTTP 200 response for DELETE /api/entries/:id
 * - Ensure transport envelope is valid and data is a proper Entry object.
 *
 * Cases:
 * - AC01 Happy path — returns the deleted Entry object
 */

import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {
    createRepository,
    mockJsonOnce,
    type RepositoryTestCtx
} from '@tests/Unit/Infrastructure/Repositories/Entries/BaseEntriesRepositoryTest';
import {ac01HappyPath as makeRequest} from '@tests/helpers/http/requests/entries/DeleteEntryRequestFactory';
import {ac01HappyPath as makeResponse} from '@tests/helpers/http/responses/entries/DeleteEntryResponseFactory';
import {ensureSuccess} from '@tests/helpers/asserts';
import type {Entry} from '@src/Domain/Models/Entries/Entry';

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
        const okResponse = ensureSuccess(res);
        const expected: Entry = okResponse.data;
        const result = await ctx.repo.deleteById(req.id);

        // Assert
        expect(result).toEqual(expected);
        expect(result.createdAt <= result.updatedAt).toBe(true);
    });
});
