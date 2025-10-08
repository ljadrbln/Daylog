// Purpose: provide consistent mocked API responses for UC-1 AddEntry gateway tests.
import {EntryFactory} from '@tests/helpers/factories/EntryFactory';
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
export function ac01HappyPath(request: AddEntryRequest): AddEntryResponse {
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
 * Builds a syntactically valid payload that violates the success=true branch
 * of UseCaseResponse by omitting the required `data` field.
 * Used to ensure the gateway rejects such responses as malformed.
 *
 * @typedef MalformedAddEntrySuccessPayload
 * A payload with success=true and missing `data` on purpose.
 */
export interface MalformedAddEntrySuccessPayload {
    success: true;
    status: number;
    // intentionally no `data`
}

/**
 * @param {AddEntryRequest} _request Accepted for signature consistency; intentionally unused.
 * @returns {MalformedAddEntrySuccessPayload} success=true payload without `data`.
 */
export function ac03MalformedJson(_request: AddEntryRequest): MalformedAddEntrySuccessPayload {
    void _request;

    const payload: MalformedAddEntrySuccessPayload = {
        success: true,
        status: 200
        // intentionally missing `data`
    };

    return payload;
}

/**
 * AC-04 — success=false with HTTP 200.
 *
 * Builds a syntactically valid response where success is false.
 * Used to assert that the gateway rejects even when status is 200.
 *
 * @param _request {AddEntryRequest} accepted for signature symmetry; intentionally unused.
 * @returns {AddEntryResponse} mocked payload with success=false.
 */
export function ac04SuccessFalse(_request: AddEntryRequest): AddEntryResponse {
    void _request;

    // prettier-ignore
    const payload: AddEntryResponse = {
        success: false,
        status : 200,
        // no data on error responses in our contract
    }

    return payload;
}
