import type {Entry} from '@src/Domain/Models/Entries/Entry';

/**
 * Interface ListEntriesPageInterface
 *
 * Purpose:
 * Shared domain-level shape for paginated entries result.
 * Single source of truth used by both the repository contract and Application DTO.
 *
 * Fields:
 * - items: list of domain entries
 * - page/perPage/total/pagesCount: pagination metadata (flat, no nested object)
 */
export interface ListEntriesPageInterface {
    items: Entry[];
    page: number;
    perPage: number;
    total: number;
    pagesCount: number;
}
