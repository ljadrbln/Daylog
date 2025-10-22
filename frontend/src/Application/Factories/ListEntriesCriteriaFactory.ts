import {ListEntriesCriteria} from '@src/Domain/Models/Entries/ListEntriesCriteria';
import type {ListEntriesRequest} from '@src/Application/DTO/Entries/ListEntries/ListEntriesRequest';

/**
 * Build domain ListEntriesCriteria from Application-level DTO.
 *
 * Mechanics:
 * - Reads optional pagination/sort/query fields from request.
 * - Applies domain defaults (page=1, perPage=10, sortField='updatedAt', sortDir='DESC').
 *
 * @param {ListEntriesRequest} req Application DTO (transport-facing).
 * @returns {ListEntriesCriteria} Domain criteria for repository queries.
 */
export class ListEntriesCriteriaFactory {
    public static fromRequest(req: ListEntriesRequest): ListEntriesCriteria {
        const page = req.page ?? 1;
        const perPage = req.perPage ?? 10;
        const field = (req.sortField ?? 'updatedAt') as 'date' | 'updatedAt';
        const dir = (req.sortDir ?? 'DESC') as 'ASC' | 'DESC';

        const criteria = new ListEntriesCriteria(
            page,
            perPage,
            req.query ?? undefined,
            req.dateFrom ?? undefined,
            req.dateTo ?? undefined,
            field,
            dir
        );
        return criteria;
    }
}
