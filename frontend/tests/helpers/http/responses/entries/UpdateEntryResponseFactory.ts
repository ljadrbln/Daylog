// Purpose: provide consistent mocked API responses for UC-5 UpdateEntry repository/gateway tests.
import {EntryFactory} from '@tests/helpers/domain/entries/EntryFactory';
import type {UpdateEntryRequest} from '@src/Application/DTO/Entries/UpdateEntry/UpdateEntryRequest';
import type {UpdateEntryResponse} from '@src/Application/DTO/Entries/UpdateEntry/UpdateEntryResponse';

/**
 * AC-01 — Happy path.
 *
 * Builds a mocked UpdateEntry success response that mirrors backend payload:
 * { success: true, status: 200, data: Entry } for PATCH /api/entries/:id.
 *
 * Mechanics:
 * - Derive updated Entry from request via EntryFactory.make(request).
 * - Wrap in a standard success envelope with status=200.
 *
 * @param {UpdateEntryRequest} request Valid UpdateEntry request payload.
 * @returns {UpdateEntryResponse} Mocked API payload with updated Entry in data.
 */
export function happyPath(request: UpdateEntryRequest): UpdateEntryResponse {
    const item = EntryFactory.make(request);

    const response: UpdateEntryResponse = {
        success: true,
        status: 200,
        data: item
    };

    return response;
}

/**
 * AC-03 — Malformed JSON (structural).
 *
 * Builds a syntactically valid payload that violates UseCaseResponse
 * by omitting the required `data` field while keeping success=true.
 *
 * @returns {object} success=true payload without `data`.
 */
export function malformed(): object {
    const payload = {
        success: true,
        status: 200
        // intentionally missing `data`
    };

    return payload;
}

/**
 * AC-04 — success=false.
 *
 * Builds a valid envelope with success=false, simulating backend-side failure
 * while transport remains HTTP 200.
 *
 * @returns {object} success=false payload with error code and message.
 */
export function successFalse(): object {
    const payload = {
        success: false,
        code: 'E_UPDATE_ENTRY',
        message: 'Failed to update entry',
        status: 200
        // no `data` on error branch by contract
    };

    return payload;
}
