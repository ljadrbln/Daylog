// Purpose: provide consistent mocked API responses for UC-2 ListEntries gateway tests.
import {EntryFactory} from '@tests/helpers/factories/EntryFactory';
import type {ListEntriesResponse} from '@src/Application/DTO/Entries/ListEntries/ListEntriesResponse';

/**
 * AC-01 — Happy Path.
 *
 * Builds a mocked ListEntries success response mirroring backend payload:
 * { success:true, status:200, data:{ items[], page, perPage, total, pagesCount } }.
 *
 * @param request Request object with pagination hints (page/perPage optional).
 * @returns {ListEntriesResponse} Mocked API payload.
 */
export function ac01HappyPath(request: {
    page?: number;
    perPage?: number;
}): ListEntriesResponse {
    // prettier-ignore
    const page    = typeof request.page === 'number' ? request.page : 1;
    const perPage = typeof request.perPage === 'number' ? request.perPage : 10;

    const itemA = EntryFactory.make({title: 'Valid title (UC-02, AC-01, #1)'});
    const itemB = EntryFactory.make({title: 'Valid title (UC-02, AC-01, #2)'});
    const itemC = EntryFactory.make({title: 'Valid title (UC-02, AC-01, #3)'});

    const items = [itemA, itemB, itemC];

    const total = items.length;
    const pagesCount = 1;

    const response: ListEntriesResponse = {
        success: true,
        status: 200,
        data: {
            items,
            page,
            perPage,
            total,
            pagesCount
        }
    };

    return response;
}

/**
 * AC-03 — Malformed JSON (structural).
 *
 * Builds a syntactically valid payload that violates the success=true branch
 * by omitting required fields inside `data` (e.g., missing `items`).
 *
 * @typedef MalformedListEntriesSuccessPayload
 * A payload with success=true and malformed/missing `data` internals.
 */
export interface MalformedListEntriesSuccessPayload {
    success: true;
    status: number;
    data: {
        // intentionally missing 'items'
        page: number;
        perPage: number;
        total: number;
        pagesCount: number;
    };
}

/**
 * @param _request Accepted for signature symmetry; intentionally unused.
 * @returns {MalformedListEntriesSuccessPayload} success=true payload with malformed data.
 */
export function ac03MalformedJson(_request: unknown): MalformedListEntriesSuccessPayload {
    void _request;

    const payload: MalformedListEntriesSuccessPayload = {
        success: true,
        status: 200,
        data: {
            // no items: Entry[]
            page: 1,
            perPage: 10,
            total: 3,
            pagesCount: 1
        }
    };

    return payload;
}

/**
 * AC-04 — success=false with HTTP 200.
 *
 * Builds a syntactically valid response where success is false.
 * Used to assert that the gateway rejects even when status is 200.
 *
 * @param _request Accepted for signature symmetry; intentionally unused.
 * @returns {ListEntriesResponse} Mocked payload with success=false.
 */
export function ac04SuccessFalse(_request: unknown): ListEntriesResponse {
    void _request;

    // prettier-ignore
    const payload: ListEntriesResponse = {
        success: false,
        status : 200,
        // no data on error responses in our contract
    };

    return payload;
}
