// UC-3 GetEntry — Request factory (AC01–AC04).
// Returns plain request objects without importing types from src.
import {EntryFactory} from '@tests/helpers/factories/EntryFactory';

/**
 * AC-01 — Happy Path request.
 *
 * Builds a request object with a valid UUID taken from EntryFactory.
 * This mirrors real-world usage where the client already knows the entry ID.
 *
 * @returns {{id: string}} Valid GetEntry request payload.
 */
export function ac01HappyPath(): {id: string} {
    const entry = EntryFactory.make({title: 'Valid title (UC-03, AC-01)'});

    const id = entry.id;
    const payload = {id};

    return payload;
}

/**
 * AC-02 — Non-2xx Response request.
 *
 * Builds a syntactically valid request used for transport-level error tests
 * (e.g., 400/404/500). The request itself is correct; only HTTP status differs.
 *
 * @returns {{id: string}} Valid request for non-2xx scenarios.
 */
export function ac02Non2xxResponse(): {id: string} {
    const entry = EntryFactory.make({title: 'Valid title (UC-03, AC-02)'});

    const id = entry.id;
    const payload = {id};

    return payload;
}

/**
 * AC-03 — Malformed JSON request.
 *
 * Builds a valid request intended for tests where the server responds
 * with a malformed JSON body (e.g., missing `data` even though success=true).
 * The request is valid by design.
 *
 * @returns {{id: string}} Valid request for malformed JSON scenarios.
 */
export function ac03MalformedJson(): {id: string} {
    const entry = EntryFactory.make({title: 'Valid title (UC-03, AC-03)'});

    const id = entry.id;
    const payload = {id};

    return payload;
}

/**
 * AC-04 — success=false request.
 *
 * Builds a valid request used in tests where the API responds with
 * { success:false } and HTTP status 200 (logical failure branch).
 *
 * @returns {{id: string}} Valid request for success=false scenarios.
 */
export function ac04SuccessFalse(): {id: string} {
    const entry = EntryFactory.make({title: 'Valid title (UC-03, AC-04)'});

    const id = entry.id;
    const payload = {id};

    return payload;
}
