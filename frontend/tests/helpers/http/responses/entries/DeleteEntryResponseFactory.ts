import type {DeleteEntryResponse} from '@src/Application/DTO/Entries/DeleteEntry/DeleteEntryResponse';
import type {DeleteEntryRequest} from '@src/Application/DTO/Entries/DeleteEntry/DeleteEntryRequest';

/**
 * Response factory for UC-4 DeleteEntry.
 * AC-01 returns success:true with a full Entry object in data.
 */
export function ac01HappyPath(req: DeleteEntryRequest): DeleteEntryResponse {
    const entry = {
        id: req.id,
        title: 'Valid title',
        body: 'Valid body',
        date: '2025-02-12',
        createdAt: '2025-10-09T12:01:57+00:00',
        updatedAt: '2025-10-09T12:02:00+00:00'
    };

    return {
        success: true,
        status: 200,
        data: entry
    };
}

/**
 * AC-03 — Malformed JSON (structural).
 *
 * Builds a syntactically valid payload that violates the success=true branch
 * of UseCaseResponse by omitting the required `data` field.
 * Used to ensure the gateway rejects such responses as malformed.
 *
 * @typedef MalformedDeleteEntrySuccessPayload
 * A payload with success=true and missing `data` on purpose.
 */
export interface MalformedDeleteEntrySuccessPayload {
    success: true;
    status: number;
    // intentionally no `data`
}

/**
 * @param {DeleteEntryRequest} _request Accepted for signature consistency; intentionally unused.
 * @returns {MalformedDeleteEntrySuccessPayload} success=true payload without `data`.
 */
export function ac03MalformedJson(
    _request: DeleteEntryRequest
): MalformedDeleteEntrySuccessPayload {
    void _request;

    const payload: MalformedDeleteEntrySuccessPayload = {
        success: true,
        status: 200
        // intentionally missing `data`
    };

    return payload;
}

/**
 * AC-04 — Contract guard payload.
 * Backend should NOT return success:false with 200; if it does, gateway must reject as malformed.
 */
export function ac04SuccessFalse(_req: DeleteEntryRequest): DeleteEntryResponse {
    void _req;

    // success:false + 200 (no data by contract on error-like responses)
    const payload: DeleteEntryResponse = {
        success: false,
        status: 200
        // code is optional per UseCaseResponse; omitted intentionally
    };

    return payload;
}
