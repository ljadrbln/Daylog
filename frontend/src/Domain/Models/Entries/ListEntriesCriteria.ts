import type {ListEntriesCriteriaInterface} from '@src/Domain/Interfaces/Entries/ListEntriesCriteriaInterface';

// Domain-owned types
// prettier-ignore
type SortDir   = 'ASC' | 'DESC';
type SortField = 'date' | 'updatedAt';

/**
 * Domain criteria for UC-2 ListEntries.
 *
 * Purpose:
 * Immutable value object carrying already-normalized list parameters
 * from Application DTO to repositories. No normalization inside — only defaults.
 *
 * Notes:
 * - Implements ListEntriesCriteriaInterface (page, perPage, query, dateFrom, dateTo).
 * - Also preserves optional sortField/sortDir for future use.
 */
export class ListEntriesCriteria implements ListEntriesCriteriaInterface {
    public readonly page: number;
    public readonly perPage: number;
    public readonly query?: string;
    public readonly dateFrom?: string;
    public readonly dateTo?: string;

    public readonly sortField: SortField;
    public readonly sortDir: SortDir;

    private constructor(
        page: number,
        perPage: number,
        query: string | undefined,
        dateFrom: string | undefined,
        dateTo: string | undefined,
        sortField: SortField,
        sortDir: SortDir
    ) {
        this.page = page;
        this.perPage = perPage;
        this.query = query;
        this.dateFrom = dateFrom;
        this.dateTo = dateTo;
        this.sortField = sortField;
        this.sortDir = sortDir;
    }
}
