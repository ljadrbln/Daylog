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
 * - AC02 Not Found (404) ⇒ returns null
 */

import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {
    createRepository,
    mockJsonOnce,
    type RepositoryTestCtx
} from '@tests/Unit/Infrastructure/Repositories/Entries/BaseEntriesRepositoryTest';
import {ac02Non2xxResponse as makeRequest} from '@tests/helpers/http/requests/entries/GetEntryRequestFactory';
import {notFound} from '@tests/helpers/http/responses/common/Non2xxResponseFactory';

describe('AC02 — EntryRepository.findById returns null on 404 Not Found', () => {
    let ctx: RepositoryTestCtx;

    beforeEach(() => {
        ctx = createRepository();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('returns null when API responds with 404 Not Found', async () => {
        // Arrange
        const request = makeRequest();

        const response = notFound();
        mockJsonOnce(ctx.fetchMock, 404, response);

        // Act
        const result = await ctx.repo.findById(request.id);

        // Assert
        expect(result).toBeNull();
    });
});
