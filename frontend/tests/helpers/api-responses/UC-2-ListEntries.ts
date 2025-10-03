// Test response factories for UC-2 List Entries.
// Purpose: build consistent API payloads for gateway unit tests.

import type { Entry } from '@src/Domain/Entries/Entry';
import type { UseCaseResponse } from '@src/Application/DTO/Common/UseCaseResponse';
import type { ListEntriesData } from '@src/Application/DTO/Entries/ListEntries/ListEntriesRequest';

/** Successful UseCaseResponse<ListEntriesData>. */
export function okList(
    items: Entry[],
    page: number = 1,
    perPage: number = 10,
): UseCaseResponse<ListEntriesData> {
    const total = items.length;
    const pagesCount = Math.max(1, Math.ceil(total / perPage));

    const data: ListEntriesData = { items, page, perPage, total, pagesCount };

    const response: UseCaseResponse<ListEntriesData> = {
        success: true,
        data,
        status: 200,
    };

    return response;
}

/** 200 OK but malformed shape (no items array). */
export function okMalformedListWithoutItems(
    page: number = 1,
    perPage: number = 10,
): UseCaseResponse<unknown> {
    const response: UseCaseResponse<unknown> = {
        success: true,
        // intentionally no items
        data: { page, perPage, total: 0, pagesCount: 0 } as unknown,
        status: 200,
    };

    return response;
}

/** 200 OK but success=false (logical failure). */
export function successFalse(
    message: string = 'logical failure',
): UseCaseResponse<ListEntriesData> {
    const response: UseCaseResponse<ListEntriesData> = {
        success: false,
        data: { items: [], page: 1, perPage: 10, total: 0, pagesCount: 1 },
        status: 200,
        message,
    };

    return response;
}
