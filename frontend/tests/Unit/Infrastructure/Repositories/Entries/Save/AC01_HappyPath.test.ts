import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {
    createRepository,
    mockJsonOnce,
    type RepositoryTestCtx
} from '@tests/Unit/Infrastructure/Repositories/Entries/BaseEntriesRepositoryTest';

import {ensureSuccess} from '@tests/helpers/asserts';

import type {Entry} from '@src/Domain/Entries/Entry';
import {ac01HappyPath as makeRequest} from '@tests/helpers/http/requests/entries/AddEntryRequestFactory';
import {ac01HappyPath as makeResponse} from '@tests/helpers/http/responses/entries/AddEntryResponseFactory';

/**
 * UC-1: Save Entry (Repository)
 * Verifies EntryRepository.save resolves with Entry when API responds 200 + {success:true,data:Entry}.
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
        const req = makeRequest();
        const res = makeResponse(req);
        mockJsonOnce(ctx.fetchMock, 200, res);

        // Act
        const okResponse = ensureSuccess(res);
        const entry: Entry = okResponse.data;
        const result = await ctx.repo.save(entry);

        // Assert
        expect(result).toEqual(entry);
        expect(result.createdAt <= result.updatedAt).toBe(true);
    });
});
