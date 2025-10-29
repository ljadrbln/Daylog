/**
 * @covers ListEntries
 *
 * Purpose:
 * Validate Application UC-2 bridges DTO → domain repo and returns a typed envelope.
 *
 * Mechanics:
 * - Mock EntryRepository.list to return a prepared page.
 * - Use ListEntriesCriteriaFactory.fromRequest to build expected criteria.
 *
 * Cases:
 * - AC01 Happy path
 */

import {describe, vi, it, expect, beforeEach} from 'vitest';
import type {EntryRepositoryInterface} from '@src/Domain/Interfaces/Entries/EntryRepositoryInterface';
import type {ListEntriesPageInterface} from '@src/Domain/Interfaces/Entries/ListEntriesPageInterface';
import {ListEntries} from '@src/Application/UseCases/Entries/ListEntries/ListEntries';
import {ListEntriesCriteriaFactory} from '@src/Application/Factories/ListEntriesCriteriaFactory';

import {ac01HappyPath as makeRequest} from '@tests/helpers/http/requests/entries/ListEntriesRequestFactory';
import {EntryFactory} from '@tests/helpers/domain/entries/EntryFactory';
import {ensureSuccess} from '@tests/helpers/asserts';

describe('AC01 — ListEntries (Application) returns success=true and page', () => {
    let repo: EntryRepositoryInterface;

    beforeEach(() => {
        const list = vi.fn();
        const findById = vi.fn();
        const deleteById = vi.fn();
        const save = vi.fn();

        repo = {list, findById, deleteById, save};
    });

    it('returns { success:true, status:200, data: page } for valid request', async () => {
        // Arrange
        const request = makeRequest();
        const criteria = ListEntriesCriteriaFactory.fromRequest(request);

        const items = [
            EntryFactory.make({title: 'Valid title #1'}),
            EntryFactory.make({title: 'Valid title #2'})
        ];

        const page: ListEntriesPageInterface = {
            items,
            page: request.page ?? 1,
            perPage: request.perPage ?? 10,
            total: 2,
            pagesCount: 1
        };

        const listMock = repo.list as unknown as ReturnType<typeof vi.fn>;
        listMock.mockResolvedValueOnce(page);

        const uc = new ListEntries(repo);

        // Act
        const response = await uc.execute(request);
        const okResponse = ensureSuccess(response);

        // Assert
        expect(okResponse.success).toBe(true);
        expect(okResponse.status).toBe(200);
        expect(okResponse.data).toEqual(page);

        expect(listMock).toHaveBeenCalledTimes(1);
        expect(listMock).toHaveBeenCalledWith(criteria);
    });
});
