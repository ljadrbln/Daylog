import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {
    createRepository,
    mockJsonOnce,
    type RepositoryTestCtx
} from '@tests/Unit/Infrastructure/Repositories/Entries/BaseEntriesRepositoryTest';

import {ac01HappyPath as makeRequest} from '@tests/helpers/http/requests/entries/ListEntriesRequestFactory';
import {ac01HappyPath as makeResponse} from '@tests/helpers/http/responses/entries/ListEntriesResponseFactory';
import type {ListEntriesPageInterface} from '@src/Domain/Interfaces/Entries/ListEntriesPageInterface';

/**
 * UC-2: Find entries by criteria (Repository)
 *
 * Purpose:
 * Ensure EntryRepository.findByCriteria resolves with a valid page when API responds
 * 200 + { success:true, data:{ items[], page, perPage, total, pagesCount } }.
 *
 * Mechanics:
 * - Build request via ListEntriesRequestFactory.ac01HappyPath().
 * - Stub a matching success envelope via ListEntriesResponseFactory.ac01HappyPath().
 * - Map DTO -> Domain (pagesCount preserved).
 *
 * @covers EntryRepository.findByCriteria
 */
describe('AC01 — EntryRepository.findByCriteria returns page (mocked)', () => {
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

        // Act
        const page = await ctx.repo.findByCriteria({
            page: request.page,
            perPage: request.perPage,
            query: request.query,
            dateFrom: request.dateFrom,
            dateTo: request.dateTo
        });

        // Assert
        const expected: ListEntriesPageInterface = {
            items: response.data!.items,
            page: response.data!.page,
            perPage: response.data!.perPage,
            total: response.data!.total,
            pagesCount: response.data!.pagesCount
        };

        expect(page).toEqual(expected);
        expect(Array.isArray(page.items)).toBe(true);
    });
});
