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
 * - AC02 Non-2xx
 */

import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {
    createRepository,
    mockJsonOnce,
    type RepositoryTestCtx
} from '@tests/Unit/Infrastructure/Repositories/Entries/BaseEntriesRepositoryTest';
import {ac02Non2xxResponse as makeRequest} from '@tests/helpers/http/requests/entries/GetEntryRequestFactory';
import {
    badRequest,
    internalServerError
} from '@tests/helpers/http/responses/common/Non2xxResponseFactory';

describe('AC02 — EntryRepository.findById throws on non-2xx response (generic)', () => {
    let ctx: RepositoryTestCtx;

    beforeEach(() => {
        ctx = createRepository();
    });
    afterEach(() => {
        ctx.cleanup();
    });

    it('throws when API responds with 400 Bad Request', async () => {
        // Arrange
        const request = makeRequest();
        const response = badRequest();
        mockJsonOnce(ctx.fetchMock, 400, response);

        // Act
        const fn = ctx.repo.findById(request.id);

        // Assert
        await expect(fn).rejects.toThrowError(/400|bad request/i);
    });

    it('throws when API responds with 500 Internal Server Error', async () => {
        // Arrange
        const request = makeRequest();
        const response = internalServerError();
        mockJsonOnce(ctx.fetchMock, 500, response);

        // Act
        const fn = ctx.repo.findById(request.id);

        // Assert
        await expect(fn).rejects.toThrowError(/500|internal/i);
    });
});
