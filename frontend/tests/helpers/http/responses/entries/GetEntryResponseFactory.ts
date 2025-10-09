// Purpose: provide consistent mocked API responses for UC-3 GetEntry gateway tests.
import {EntryFactory} from '@tests/helpers/factories/EntryFactory';
import type {GetEntryRequest} from '@src/Application/DTO/Entries/GetEntry/GetEntryRequest';
import type {GetEntryResponse} from '@src/Application/DTO/Entries/GetEntry/GetEntryResponse';

/**
 * AC-01 — Happy path.
 *
 * Builds a mocked GetEntry success response mirroring backend payload:
 * { success: true, status: 200, data: Entry }.
 *
 * @param {GetEntryRequest} request Valid GetEntry request payload.
 * @returns {GetEntryResponse} Mocked API payload with Entry in `data`.
 */
export function ac01HappyPath(request: GetEntryRequest): GetEntryResponse {
    const item = EntryFactory.make({id: request.id});

    const response: GetEntryResponse = {
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
 * @typedef MalformedGetEntrySuccessPayload
 * A payload with success=true and missing `data` on purpose.
 */
export interface MalformedGetEntrySuccessPayload {
    success: true;
    status: number;
    // intentionally no `data`
}

/**
 * @param {GetEntryRequest} _request Accepted for signature consistency; intentionally unused.
 * @returns {MalformedGetEntrySuccessPayload} success=true payload without `data`.
 */
export function ac03MalformedJson(_request: GetEntryRequest): MalformedGetEntrySuccessPayload {
    void _request;

    const payload: MalformedGetEntrySuccessPayload = {
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
 * @param {GetEntryRequest} _request Accepted for signature symmetry; intentionally unused.
 * @returns {GetEntryResponse} Mocked payload with success=false.
 */
export function ac04SuccessFalse(_request: GetEntryRequest): GetEntryResponse {
    void _request;

    // prettier-ignore
    const payload: GetEntryResponse = {
        success: false,
        status : 200,
        // no data on error responses in our contract
    };

    return payload;
}
