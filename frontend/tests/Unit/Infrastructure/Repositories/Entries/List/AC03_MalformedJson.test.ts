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
 * - AC03 Malformed JSON
 */

import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {
    createRepository,
    mockJsonOnce,
    type RepositoryTestCtx
} from '@tests/Unit/Infrastructure/Repositories/Entries/BaseEntriesRepositoryTest';

import {ac03MalformedJson as makeRequest} from '@tests/helpers/http/requests/entries/GetEntryRequestFactory';
import {ac03MalformedJson as makeResponse} from '@tests/helpers/http/responses/entries/GetEntryResponseFactory';

describe('AC03 — EntryRepository.findById rejects on malformed JSON', () => {
    let ctx: RepositoryTestCtx;

    beforeEach(() => {
        ctx = createRepository();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('throws when response shape is invalid for GET /api/entries/:id', async () => {
        // Arrange
        const req = makeRequest();
        const res = makeResponse(req);
        mockJsonOnce(ctx.fetchMock, 200, res);

        // Act
        const fn = ctx.repo.findById(req.id);

        // Assert
        const message = 'Malformed response for GET /api/entries/:id';
        await expect(fn).rejects.toThrow(message);
    });
});
