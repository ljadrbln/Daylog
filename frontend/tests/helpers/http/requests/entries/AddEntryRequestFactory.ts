// UC-1 AddEntry — Request factory (AC01–AC04).
import {EntryFactory} from '@tests/helpers/domain/entries/EntryFactory';
import type {AddEntryRequest} from '@src/Application/DTO/Entries/AddEntry/AddEntryRequest';

/**
 * Builds a `{title, body, date}` request payload using EntryFactory as the single source of truth.
 *
 * Purpose: generate a valid AddEntryRequest without hard-coded data; the title embeds UC/AC
 * for traceability in test data.
 *
 * @param {string} uc Use case label, e.g. "UC-04".
 * @param {string} ac Acceptance criteria label, e.g. "AC-02".
 *
 * @returns {AddEntryRequest} Request payload with a valid entry id.
 */
function getPayload(uc: string, ac: string): AddEntryRequest {
    const title = `Valid title (${uc}, ${ac})`;
    const entry = EntryFactory.make({title});

    const body = entry.body;
    const date = entry.date;

    const payload: AddEntryRequest = {title, body, date};

    return payload;
}

/**
 * AC-01 — Happy Path payload.
 *
 * Builds a request object directly from EntryFactory output.
 * Extracts title, body, and date via destructuring for consistency
 * with domain Entry shape.
 *
 * @return {AddEntryRequest} Valid AddEntry request body.
 */
export function ac01HappyPath(): AddEntryRequest {
    const payload = getPayload('UC-01', 'AC-01');

    return payload;
}

/**
 * AC-02 — Non-2xx Response payload.
 *
 * Builds a syntactically valid AddEntry request intended
 * for transport-level error tests (HTTP 400/500).
 * Extracts only title, body, and date from EntryFactory output
 * to stay consistent with domain Entry shape.
 *
 * @return {AddEntryRequest} Valid request for non-2xx tests.
 */
export function ac02Non2xxResponse(): AddEntryRequest {
    const payload = getPayload('UC-01', 'AC-02');

    return payload;
}

/**
 * AC-03 — Malformed JSON payload.
 *
 * Builds a syntactically valid AddEntry request intended
 * for tests where the server responds with a malformed JSON body
 * (e.g., missing data.item or invalid shape).
 * The request itself is completely valid; only the response is corrupted.
 *
 * @return {AddEntryRequest} Valid AddEntry request for malformed JSON tests.
 */
export function ac03MalformedJson(): AddEntryRequest {
    const payload = getPayload('UC-01', 'AC-03');

    return payload;
}

/**
 * AC-04 — Success=false payload.
 *
 * Builds a fully valid AddEntry request used in tests
 * where the server responds with { success: false } but HTTP status is 200.
 * The request itself is correct; only the logical outcome differs.
 *
 * @return {AddEntryRequest} Valid AddEntry request for success=false tests.
 */
export function ac04SuccessFalse(): AddEntryRequest {
    const payload = getPayload('UC-01', 'AC-04');

    return payload;
}
