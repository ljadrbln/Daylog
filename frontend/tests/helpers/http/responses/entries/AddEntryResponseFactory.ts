// Purpose: provide consistent mocked API responses for UC-1 AddEntry gateway tests.
import {EntryFactory} from '@tests/helpers/factories/EntryFactory';
import type {UseCaseResponse} from '@src/Application/DTO/Common/UseCaseResponse';
import type {AddEntryRequest} from '@src/Application/DTO/Entries/AddEntry/AddEntryRequest';
import type {AddEntryResponse} from '@src/Application/DTO/Entries/AddEntry/AddEntryResponse';

/**
 * AC-01 — Happy path.
 *
 * Builds a mocked API response representing a successful AddEntry request.
 * Mirrors the real backend payload:
 *
 * ```json
 * {
 *   "success": true,
 *   "status": 200,
 *   "data": { ...Entry }
 * }
 * ```
 *
 * The request must include valid `title`, `body`, and `date` fields.
 * EntryFactory is used to generate a realistic Entry object.
 *
 * @param {AddEntryRequest} request Valid AddEntry request payload.
 * @returns {AddEntryResponse} Mocked API payload for a successful entry creation.
 */
export function ac01HappyPath(request: AddEntryRequest): AddEntryResponse {
    const item = EntryFactory.make(request);

    // prettier-ignore
    const response: AddEntryResponse = {
        success: true,
        status : 200,
        data   : item
    };

    return response;
}

/**
 * AC-03 — Malformed JSON (structural).
 *
 * Builds a syntactically valid but structurally invalid AddEntry response
 * used to verify gateway validation of malformed payloads.
 *
 * The response emulates a 200 OK with `success=true` but missing the `data`
 * object that normally contains the created Entry.
 *
 * ```json
 * {
 *   "success": true,
 *   "status": 200
 * }
 * ```
 *
 * @param {AddEntryRequest} _request Accepted for signature consistency; intentionally unused.
 * @returns {UseCaseResponse<unknown>} Mocked malformed response to trigger gateway rejection.
 */
export function ac03MalformedJson(_request: AddEntryRequest): UseCaseResponse<unknown> {
    void _request;

    // prettier-ignore
    const payload: AddEntryResponse = {
        success: true,
        status : 200
        // intentionally missing data
    };

    return payload;
}
