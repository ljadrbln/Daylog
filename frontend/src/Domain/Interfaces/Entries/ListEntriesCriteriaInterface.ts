/**
 * ListEntriesCriteriaInterface
 *
 * Purpose:
 * Domain-level criteria for listing entries.
 * Mirrors backend ListEntriesCriteria model — defines filters and pagination params.
 *
 * Fields:
 * - page: 1-based index.
 * - perPage: page size.
 * - query: free-text search.
 * - dateFrom/dateTo: ISO-8601 YYYY-MM-DD range filter.
 */
export interface ListEntriesCriteriaInterface {
    page?: number;
    perPage?: number;
    query?: string;
    dateFrom?: string;
    dateTo?: string;
    sortField?: 'date' | 'createdAt' | 'updatedAt';
    sortDir?: 'ASC' | 'DESC';
}
