import {EntryFactory} from '@tests/helpers/factories/EntryFactory';
import type {UpdateEntryRequest} from '@src/Application/DTO/Entries/UpdateEntry/UpdateEntryRequest';

/**
 * Builds a typed UpdateEntryRequest payload for UC-5 cases.
 *
 * Purpose: generate a valid partial update request (id + fields to change)
 * without literals at call site; the title embeds UC/AC for traceability.
 *
 * Notes:
 * - At least one updatable field must be present (title/body/date).
 */
function getPayload(uc: string, ac: string): UpdateEntryRequest {
    const base = EntryFactory.make(); // source of truth for valid fields

    const id    = base.id;
    const title = `Valid title (updated) (${uc}, ${ac})`;
    const body  = base.body;
    const date  = base.date;

    const payload: UpdateEntryRequest = {id, title, body, date};
    return payload;
}

/**
 * AC-01 — Happy Path payload.
 *
 * @returns {UpdateEntryRequest} Valid request for success scenario.
 */
export function ac01HappyPath(): UpdateEntryRequest {
    const payload = getPayload('UC-05', 'AC-01');

    return payload;
}

/**
 * AC-02 — Non-2xx Response payload.
 *
 * @returns {UpdateEntryRequest} Valid request for generic transport errors.
 */
export function ac02Non2xxResponse(): UpdateEntryRequest {
    const payload = getPayload('UC-05', 'AC-02');

    return payload;
}

/**
 * AC-03 — Malformed JSON payload.
 *
 * @returns {UpdateEntryRequest} Valid request; response will be malformed.
 */
export function ac03MalformedJson(): UpdateEntryRequest {
    const payload = getPayload('UC-05', 'AC-03');

    return payload;
}

/**
 * AC-04 — success=false with 200 payload.
 *
 * @returns {UpdateEntryRequest} Valid request; response has success=false.
 */
export function ac04SuccessFalse(): UpdateEntryRequest {
    const payload = getPayload('UC-05', 'AC-04');

    return payload;
}
