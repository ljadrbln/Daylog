import {describe, it, expect} from 'vitest';

import {ListEntriesCriteria} from '@src/Domain/Models/Entries/ListEntriesCriteria';
import {ac01HappyPath as makeRequest} from '@tests/helpers/http/requests/entries/ListEntriesRequestFactory';
import type {ListEntriesRequest} from '@src/Application/DTO/Entries/ListEntries/ListEntriesRequest';

/**
 * UC-2: ListEntriesCriteria — factory from Request DTO (happy path).
 *
 * Purpose:
 * Ensure ListEntriesCriteria.fromRequest copies normalized fields from
 * ListEntriesRequest and applies safe defaults for missing values.
 *
 * Mechanics:
 * - Build a valid ListEntriesRequest via factory (page/perPage/query/sort* present).
 * - Call ListEntriesCriteria.fromRequest(request) to create a domain criteria object.
 * - Verify 1:1 mapping of fields and default values for sort if omitted by DTO.
 *
 * @covers ListEntriesCriteria.fromRequest
 */
describe('AC01 — ListEntriesCriteria.fromRequest builds domain criteria (happy path)', () => {
    it('copies fields from request and keeps defaults', () => {
        // Arrange
        const request: ListEntriesRequest = makeRequest();

        // Act
        const criteria = ListEntriesCriteria.fromRequest(request);

        // Assert
        const expectedPage = request.page ?? 1;
        const expectedPerPage = request.perPage ?? 10;
        const expectedQuery = request.query ?? undefined;
        const expectedDateFrom = request.dateFrom ?? undefined;
        const expectedDateTo = request.dateTo ?? undefined;
        const expectedSortField = request.sortField ?? 'updatedAt';
        const expectedSortDir = request.sortDir ?? 'DESC';

        expect(criteria.page).toBe(expectedPage);
        expect(criteria.perPage).toBe(expectedPerPage);
        expect(criteria.query).toBe(expectedQuery);
        expect(criteria.dateFrom).toBe(expectedDateFrom);
        expect(criteria.dateTo).toBe(expectedDateTo);
        expect(criteria.sortField).toBe(expectedSortField);
        expect(criteria.sortDir).toBe(expectedSortDir);
    });
});
