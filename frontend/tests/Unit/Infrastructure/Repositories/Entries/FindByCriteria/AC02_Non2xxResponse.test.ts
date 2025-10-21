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

/**
 * UC-3: Get Entry (Repository)
 *
 * Purpose:
 * Verify that EntryRepository.findById rejects on generic non-2xx HTTP responses (400/500).
 * Mechanics:
 * - Build request via factory.
 * - Enqueue JSON payload with 400/500 status.
 * - Expect rejection with a readable message containing the status.
 *
 * @covers EntryRepository.findById
 */
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
        const req = makeRequest();
        mockJsonOnce(ctx.fetchMock, 400, badRequest());

        // Act
        const fn = ctx.repo.findById(req.id);

        // Assert
        await expect(fn).rejects.toThrow(/400|bad request/i);
    });

    it('throws when API responds with 500 Internal Server Error', async () => {
        // Arrange
        const req = makeRequest();
        mockJsonOnce(ctx.fetchMock, 500, internalServerError());

        // Act
        const fn = ctx.repo.findById(req.id);

        // Assert
        await expect(fn).rejects.toThrow(/500|internal/i);
    });
});
