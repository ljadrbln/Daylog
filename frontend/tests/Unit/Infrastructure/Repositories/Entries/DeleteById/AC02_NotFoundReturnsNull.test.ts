/**
 * AC-02 — EntryRepository.deleteById returns null on 404 Not Found (Infrastructure).
 *
 * Purpose:
 * Ensure transport-level 404 with a structured JSON body
 * ({ success:false, status:404, code:'ENTRY_NOT_FOUND' }) is normalized by the repository
 * into `null`, so Application-level UC maps it to a 404 envelope.
 *
 * Mechanics:
 * - Mock HTTP DELETE /api/entries/:id to return 404 with JSON body.
 * - Call repo.deleteById(id).
 * - Expect `null`.
 *
 * Cases:
 * - Not found → null
 */

import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {
    createRepository,
    mockJsonOnce,
    type RepositoryTestCtx
} from '@tests/Unit/Infrastructure/Repositories/Entries/BaseEntriesRepositoryTest';
import {ac02Non2xxResponse as makeRequest} from '@tests/helpers/http/requests/entries/DeleteEntryRequestFactory';
import {notFound} from '@tests/helpers/http/responses/common/Non2xxResponseFactory';

describe('AC02 — EntryRepository.deleteById returns null on 404 Not Found', () => {
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
        const result = await ctx.repo.deleteById(request.id);

        // Assert
        expect(result).toBeNull();
    });
});
