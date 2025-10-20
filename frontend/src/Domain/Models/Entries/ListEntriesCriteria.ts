import type {ListEntriesCriteriaInterface} from '@src/Domain/Interfaces/Entries/ListEntriesCriteriaInterface';
import type {ListEntriesRequest} from '@src/Application/DTO/Entries/ListEntries/ListEntriesRequest';

type SortField = NonNullable<ListEntriesRequest['sortField']>; // 'date' | 'updatedAt'
type SortDir   = NonNullable<ListEntriesRequest['sortDir']>;   // 'ASC' | 'DESC'

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

    /**
     * Build criteria from Application Request DTO using safe defaults.
     *
     * Defaults:
     * - page=1, perPage=10
     * - sortField='updatedAt', sortDir='DESC'
     *
     * @param {ListEntriesRequest} req Request DTO with filters/paging/sort.
     * @returns {ListEntriesCriteria} Immutable criteria.
     */
    public static fromRequest(req: ListEntriesRequest): ListEntriesCriteria {
        const page = req.page ?? 1;
        const perPage = req.perPage ?? 10;

        const query = req.query ?? undefined;
        const dateFrom = req.dateFrom ?? undefined;
        const dateTo = req.dateTo ?? undefined;

        const sortField: SortField = req.sortField ?? 'updatedAt';
        const sortDir: SortDir = req.sortDir ?? 'DESC';

        const criteria = new ListEntriesCriteria(
            page,
            perPage,
            query,
            dateFrom,
            dateTo,
            sortField,
            sortDir
        );

        return criteria;
    }
}
