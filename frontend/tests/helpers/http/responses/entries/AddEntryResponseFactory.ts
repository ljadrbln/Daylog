// Purpose: provide consistent mocked API responses for UC-1 AddEntry gateway tests.
import {EntryFactory} from '@tests/helpers/domain/entries/EntryFactory';
import type {AddEntryRequest} from '@src/Application/DTO/Entries/AddEntry/AddEntryRequest';
import type {AddEntryResponse} from '@src/Application/DTO/Entries/AddEntry/AddEntryResponse';

/**
 * AC-01 — Happy path.
 *
 * Builds a mocked AddEntry success response that mirrors backend payload:
 * {success: true, status: 200, data: Entry}.
 *
 * @param {AddEntryRequest} request Valid AddEntry request payload.
 * @returns {AddEntryResponse} Mocked API payload with created Entry in data.
 */
export function happyPath(request: AddEntryRequest): AddEntryResponse {
    const item = EntryFactory.make(request);

    const response: AddEntryResponse = {
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
 * by omitting the required `data` field.
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
 * Builds a valid envelope with success=false, simulating backend failure.
 *
 * @returns {object} success=false payload with error code and message.
 */
export function successFalse(): object {
    const payload = {
        success: false,
        code: 'E_ADD_ENTRY',
        message: 'Failed to add entry',
        status: 200
    };

    return payload;
}
