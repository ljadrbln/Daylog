import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {
    createRepository,
    mockJsonOnce,
    type RepositoryTestCtx
} from '@tests/Unit/Infrastructure/Repositories/Entries/BaseEntriesRepositoryTest';

import {ac03MalformedJson as makeRequest} from '@tests/helpers/http/requests/entries/DeleteEntryRequestFactory';
import {ac03MalformedJson as makeResponse} from '@tests/helpers/http/responses/entries/DeleteEntryResponseFactory';

/**
 * UC-4: Delete Entry (Repository)
 *
 * Purpose:
 * Verify that EntryRepository.deleteById rejects when API responds
 * with malformed JSON for DELETE /api/entries/:id (e.g., missing required fields).
 *
 * Mechanics:
 * - Enqueue a 200 OK response with an invalid success envelope.
 * - Expect rejection with the canonical "Malformed response" message.
 *
 * @covers EntryRepository.deleteById
 */
describe('AC03 — EntryRepository.deleteById rejects on malformed JSON', () => {
    let ctx: RepositoryTestCtx;

    beforeEach(() => {
        ctx = createRepository();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('throws when response shape is invalid for DELETE /api/entries/:id', async () => {
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
