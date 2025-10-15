import type {UseCaseResponse} from '@src/Application/DTO/Common/UseCaseResponse';
import type {ListEntriesPageInterface} from '@src/Domain/Interfaces/Entries/ListEntriesPageInterface';

/**
 * UC-2: List Entries — response DTO.
 *
 * Mirrors backend payload exactly:
 * { success: true, status: 200, data: { items, page, perPage, total, pagesCount } }.
 * Pagination fields are top-level inside `data` (no nested `pagination` object).
 */
export type ListEntriesData = ListEntriesPageInterface;

/**
 * Discriminated transport envelope for UC-2.
 */
export type ListEntriesResponse = UseCaseResponse<ListEntriesData>;
