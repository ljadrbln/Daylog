// Test response factories for UC-1 Add Entry.
// Purpose: build consistent API payloads for gateway unit tests.

import type {Entry} from '@src/Domain/Entries/Entry';
import type {UseCaseResponse} from '@src/Application/DTO/Common/UseCaseResponse';

/**
 * Successful UseCaseResponse<{item: Entry}>.
 *
 * Used in AC-01 (Happy Path) where:
 *  - HTTP status = 200;
 *  - success = true;
 *  - data.item contains the created entry.
 */
export function okPost(item: Entry): UseCaseResponse<{item: Entry}> {
    const response: UseCaseResponse<{item: Entry}> = {
        success: true,
        data: {item},
        status: 200
    };

    return response;
}

/**
 * 200 OK but malformed shape (no data.item object).
 */
export function okMalformedPostWithoutItem(): UseCaseResponse<unknown> {
    const response: UseCaseResponse<unknown> = {
        success: true,
        data: {} as unknown,
        status: 200
    };

    return response;
}

/**
 * 200 OK but success=false (logical failure on server side).
 */
export function successFalsePost(item: Entry): UseCaseResponse<{item: Entry}> {
    const response: UseCaseResponse<{item: Entry}> = {
        success: false,
        data: {item},
        status: 200
    };

    return response;
}
