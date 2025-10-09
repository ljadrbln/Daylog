/**
 * UC-2: List Entries — request DTO.
 *
 * Describes query parameters accepted by /api/entries.
 * The gateway serializes this DTO into a query string.
 * - `query` accepts a single string.
 * - Date range uses strict YYYY-MM-DD (backend validates semantics).
 * - Sorting is explicit to prevent accidental typos.
 */
export type ListEntriesRequest = {
    /**
     * Free-text search.
     */
    query?: string;

    /**
     * 1-based page index.
     */
    page?: number;

    /**
     * Page size.
     */
    perPage?: number;

    /**
     * Sort field allowed by backend contract.
     */
    sortField?: 'date' | 'updatedAt';

    /**
     * Sort direction.
     */
    sortDir?: 'ASC' | 'DESC';

    /**
     * Inclusive date range (YYYY-MM-DD).
     */
    dateFrom?: string;
    dateTo?: string;
};
