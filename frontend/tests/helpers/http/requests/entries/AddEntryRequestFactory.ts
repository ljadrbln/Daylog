// UC-1 AddEntry — Request factory (AC01–AC04).
// Returns plain request objects without importing types from src.
import {EntryFactory} from '@tests/helpers/factories/EntryFactory';

/**
 * AC-01 — Happy Path payload.
 *
 * Builds a request object directly from EntryFactory output.
 * Extracts title, body, and date via destructuring for consistency
 * with domain Entry shape.
 *
 * @return {{title: string, body: string, date: string}} Valid AddEntry request body.
 */
export function ac01HappyPath(): {title: string; body: string; date: string} {
    const entry = EntryFactory.make({title: 'Valid title (UC-01, AC-01)'});

    const {title, body, date} = entry;
    const payload = {title, body, date};

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
 * @return {{title: string, body: string, date: string}} Valid request for non-2xx tests.
 */
export function ac02Non2xxResponse(): {title: string; body: string; date: string} {
    const entry = EntryFactory.make({title: 'Valid title (UC-01, AC-02)'});

    const {title, body, date} = entry;

    const payload = {title, body, date};
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
 * @return {{title: string, body: string, date: string}} Valid AddEntry request for malformed JSON tests.
 */
export function ac03MalformedJson(): {title: string; body: string; date: string} {
    const entry = EntryFactory.make({title: 'Valid title (UC-01, AC-03)'});

    const {title, body, date} = entry;

    const payload = {title, body, date};
    return payload;
}

/**
 * AC-04 — Success=false payload.
 *
 * Builds a fully valid AddEntry request used in tests
 * where the server responds with { success: false } but HTTP status is 200.
 * The request itself is correct; only the logical outcome differs.
 *
 * @return {{title: string, body: string, date: string}} Valid AddEntry request for success=false tests.
 */
export function ac04SuccessFalse(): {title: string; body: string; date: string} {
    const entry = EntryFactory.make({title: 'Valid title (UC-01, AC-04)'});

    const {title, body, date} = entry;

    const payload = {title, body, date};
    return payload;
}
