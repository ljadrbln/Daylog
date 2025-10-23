/**
 * @covers EntryRepository.list
 *
 * Purpose:
 * Validate repository behavior for UC-2.
 *
 * Mechanics:
 * - Mock HTTP responses and assert repository invariants.
 *
 * Cases:
 * - AC04 success=false
 */

import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {
    createRepository,
    mockJsonOnce,
    type RepositoryTestCtx
} from '@tests/Unit/Infrastructure/Repositories/Entries/BaseEntriesRepositoryTest';

import {ac04SuccessFalse as makeRequest} from '@tests/helpers/http/requests/entries/GetEntryRequestFactory';
import {ac04SuccessFalse as makeResponse} from '@tests/helpers/http/responses/entries/GetEntryResponseFactory';

/**
 * UC-3: Get Entry (Repository)
 *
 * Purpose:
 * Verify that EntryRepository.findById rejects when API responds
 * with { success:false } despite HTTP 200.
 *
 * Mechanics:
 * - Enqueue 200 OK + {success:false, errors:[…]}.
 * - Expect rejection with the canonical "Malformed response" message.
 *
 * @covers EntryRepository.findById
 */
describe('AC04 — EntryRepository.findById rejects when success=false with 200 OK', () => {
    let ctx: RepositoryTestCtx;

    beforeEach(() => {
        ctx = createRepository();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('throws when API responds with success=false despite 200', async () => {
        // Arrange
        const req = makeRequest();
        const res = makeResponse(req); // success:false
        mockJsonOnce(ctx.fetchMock, 200, res);

        // Act
        const fn = ctx.repo.findById(req.id);

        // Assert
        const message = 'Malformed response for GET /api/entries/:id';
        await expect(fn).rejects.toThrow(message);
    });
});
