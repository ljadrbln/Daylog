// Test response factories for UC-3 Get Entry.
// Purpose: build consistent API payloads for gateway unit tests.

import type {Entry} from '@src/Domain/Entries/Entry';
import type {UseCaseResponse} from '@src/Application/DTO/Common/UseCaseResponse';

/**
 * Successful UseCaseResponse<{item: Entry}>.
 *
 * Used in AC-01 (Happy Path) where:
 *  - HTTP status = 200;
 *  - success = true;
 *  - data.item is a valid object.
 *
 * @param {Entry} item Entry domain object returned by API.
 * @returns {UseCaseResponse<{item: Entry}>} success payload.
 */
export function okGet(item: Entry): UseCaseResponse<{item: Entry}> {
    const response: UseCaseResponse<{item: Entry}> = {
        success: true,
        data: {item},
        status: 200
    };

    return response;
}

/**
 * 200 OK but malformed shape (no data.item object).
 *
 * Used in AC-03 tests to simulate transport-level malformed response.
 *
 * @returns {UseCaseResponse<unknown>} malformed payload.
 */
export function okMalformedGetWithoutItem(): UseCaseResponse<unknown> {
    const response: UseCaseResponse<unknown> = {
        success: true,
        // intentionally missing data.item
        data: {} as unknown,
        status: 200
    };

    return response;
}

/**
 * 200 OK but success=false (logical failure on server side).
 *
 * Used in AC-04 tests — treated as malformed in frontend gateway.
 *
 * @param {Entry} item Optional entry to include under data.item.
 * @returns {UseCaseResponse<{item: Entry}>} failure payload.
 */
export function successFalseGet(item: Entry): UseCaseResponse<{item: Entry}> {
    const response: UseCaseResponse<{item: Entry}> = {
        success: false,
        data: {item},
        status: 200
    };

    return response;
}
