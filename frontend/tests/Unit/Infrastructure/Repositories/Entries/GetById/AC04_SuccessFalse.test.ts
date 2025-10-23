/**
 * @covers EntryRepository.findById
 *
 * Purpose:
 * Validate repository behavior for UC-3.
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

describe('AC04 — EntryRepository.findById rejects when success=false with 200 OK', () => {
    let ctx: RepositoryTestCtx;

    beforeEach(() => {
        ctx = createRepository();
    });
    afterEach(() => {
        ctx.cleanup();
    });

    it('throws when API responds with success=false despite 200 status', async () => {
        // Arrange
        const request = makeRequest();
        const response = makeResponse(request); // success:false
        mockJsonOnce(ctx.fetchMock, 200, response);

        // Act
        const fn = ctx.repo.findById(request.id);
        const message = 'Malformed response for GET /api/entries/:id';

        // Assert
        await expect(fn).rejects.toThrowError(message);
    });
});
