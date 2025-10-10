// UC-3 GetEntry — Request factory (AC01–AC04).
import {EntryFactory} from '@tests/helpers/factories/EntryFactory';
import type {GetEntryRequest} from '@src/Application/DTO/Entries/GetEntry/GetEntryRequest';

/**
 * Builds a `{ id }` request payload using EntryFactory as the single source of truth.
 *
 * Purpose: generate a valid GetEntryRequest without hard-coded UUIDs; the title embeds UC/AC
 * for traceability in test data.
 *
 * @param {string} uc Use case label, e.g. "UC-04".
 * @param {string} ac Acceptance criteria label, e.g. "AC-02".
 *
 * @returns {GetEntryRequest} Request payload with a valid entry id.
 */
function getPayload(uc: string, ac: string): GetEntryRequest {
    const title = `Valid title (${uc}, ${ac})`;
    const entry = EntryFactory.make({title});

    const id = entry.id;
    const payload: GetEntryRequest = {id};

    return payload;
}

/**
 * AC-01 — Happy Path request.
 *
 * Builds a request object with a valid UUID taken from EntryFactory.
 * This mirrors real-world usage where the client already knows the entry ID.
 *
 * @returns {GetEntryRequest} Valid GetEntry request payload.
 */
export function ac01HappyPath(): {id: string} {
    const payload = getPayload('UC-03', 'AC-01');

    return payload;
}

/**
 * AC-02 — Non-2xx Response request.
 *
 * Builds a syntactically valid request used for transport-level error tests
 * (e.g., 400/404/500). The request itself is correct; only HTTP status differs.
 *
 * @returns {GetEntryRequest} Valid request for non-2xx scenarios.
 */
export function ac02Non2xxResponse(): {id: string} {
    const payload = getPayload('UC-03', 'AC-02');

    return payload;
}

/**
 * AC-03 — Malformed JSON request.
 *
 * Builds a valid request intended for tests where the server responds
 * with a malformed JSON body (e.g., missing `data` even though success=true).
 * The request is valid by design.
 *
 * @returns {GetEntryRequest} Valid request for malformed JSON scenarios.
 */
export function ac03MalformedJson(): {id: string} {
    const payload = getPayload('UC-03', 'AC-03');

    return payload;
}

/**
 * AC-04 — success=false request.
 *
 * Builds a valid request used in tests where the API responds with
 * { success:false } and HTTP status 200 (logical failure branch).
 *
 * @returns {GetEntryRequest} Valid request for success=false scenarios.
 */
export function ac04SuccessFalse(): {id: string} {
    const payload = getPayload('UC-03', 'AC-04');

    return payload;
}
