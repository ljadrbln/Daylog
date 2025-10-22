import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {
    createRepository,
    mockJsonOnce,
    type RepositoryTestCtx
} from '@tests/Unit/Infrastructure/Repositories/Entries/BaseEntriesRepositoryTest';

import {ac01HappyPath as makeRequest} from '@tests/helpers/http/requests/entries/ListEntriesRequestFactory';
import {ac01HappyPath as makeResponse} from '@tests/helpers/http/responses/entries/ListEntriesResponseFactory';
import {ListEntriesCriteriaFactory} from '@src/Application/Factories/ListEntriesCriteriaFactory';
import type {ListEntriesPageInterface} from '@src/Domain/Interfaces/Entries/ListEntriesPageInterface';
import {ensureSuccess} from '@tests/helpers/asserts';

/**
 * UC-2: Find entries by criteria (Repository)
 *
 * Purpose:
 * Ensure EntryRepository.list resolves with a valid page when API responds
 * 200 + { success:true, data:{ items[], page, perPage, total, pagesCount } }.
 *
 * Mechanics:
 * - Build request via ListEntriesRequestFactory.ac01HappyPath().
 * - Convert request → domain criteria (ListEntriesCriteria.fromRequest()).
 * - Stub success envelope via ListEntriesResponseFactory.ac01HappyPath().
 * - Narrow response to success-branch via ensureSuccess() to avoid union-type issues.
 *
 * @covers EntryRepository.list
 */
describe('AC01 — EntryRepository.list returns page (mocked)', () => {
    let ctx: RepositoryTestCtx;

    beforeEach(() => {
        ctx = createRepository();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('returns page for valid criteria', async () => {
        // Arrange
        const request = makeRequest();
        const response = makeResponse(request);
        mockJsonOnce(ctx.fetchMock, 200, response);

        const criteria = ListEntriesCriteriaFactory.fromRequest(request);

        // Act
        const page = await ctx.repo.list(criteria);

        // Assert
        const ok = ensureSuccess(response);
        const expected: ListEntriesPageInterface = {
            items: ok.data.items,
            page: ok.data.page,
            perPage: ok.data.perPage,
            total: ok.data.total,
            pagesCount: ok.data.pagesCount
        };

        expect(page).toEqual(expected);
        expect(Array.isArray(page.items)).toBe(true);
    });
});
