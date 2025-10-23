/**
 * @covers EntryRepository.deleteById
 *
 * Purpose:
 * Validate repository behavior for UC-4.
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

import {ac04SuccessFalse as makeRequest} from '@tests/helpers/http/requests/entries/DeleteEntryRequestFactory';
import {ac04SuccessFalse as makeResponse} from '@tests/helpers/http/responses/entries/DeleteEntryResponseFactory';

describe('AC04 — EntryRepository.deleteById rejects when success=false with 200 OK', () => {
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
        const res = makeResponse();
        mockJsonOnce(ctx.fetchMock, 200, res);

        // Act
        const fn = ctx.repo.deleteById(req.id);

        // Assert
        const message = 'Malformed response for DELETE /api/entries/:id';
        await expect(fn).rejects.toThrowError(message);
    });
});
