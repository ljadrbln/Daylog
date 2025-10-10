// UC-2 ListEntries — Request factory (AC01–AC04).
import type {ListEntriesRequest} from '@src/Application/DTO/Entries/ListEntries/ListEntriesRequest';

/**
 * Builds a ListEntriesRequest payload using a single builder to keep scenarios consistent.
 *
 * Purpose: generate valid request objects without hard-coded literals at call sites.
 * The `query` embeds UC/AC labels for traceability in test data.
 *
 * @param {string} uc Use case label, e.g., "UC-02".
 * @param {string} ac Acceptance criteria label, e.g., "AC-01".
 * @param {Partial<ListEntriesRequest>} overrides Optional per-scenario overrides (e.g., sortField).
 * @returns {ListEntriesRequest} Valid ListEntries request payload.
 */
function getPayload(
    uc: string,
    ac: string,
    overrides: Partial<ListEntriesRequest> = {}
): ListEntriesRequest {
    const query = `valid query (${uc}, ${ac})`;
    const page = 1;
    const perPage = 10;
    const sortField = 'updatedAt' as const;
    const sortDir = 'DESC' as const;

    const base: ListEntriesRequest = {
        query,
        page,
        perPage,
        sortField,
        sortDir
        // dateFrom/dateTo are optional
    };

    const payload: ListEntriesRequest = {...base, ...overrides};

    return payload;
}

/**
 * AC-01 — Happy Path request.
 *
 * Builds a valid query with explicit pagination and default sorting by updatedAt DESC.
 * Mirrors a typical client request to fetch the latest entries.
 *
 * @returns {ListEntriesRequest} Valid ListEntries request.
 */
export function ac01HappyPath(): ListEntriesRequest {
    const payload = getPayload('UC-02', 'AC-01');

    return payload;
}

/**
 * AC-02 — Non-2xx Response request.
 *
 * Builds a syntactically valid request intended for transport-level error tests (HTTP 400/500).
 * Uses sorting by date DESC to differ from AC-01 while remaining valid.
 *
 * @returns {ListEntriesRequest} Valid request for non-2xx tests.
 */
export function ac02Non2xxResponse(): ListEntriesRequest {
    const overrides: Partial<ListEntriesRequest> = {sortField: 'date'};

    const payload = getPayload('UC-02', 'AC-02', overrides);
    return payload;
}

/**
 * AC-03 — Malformed JSON request.
 *
 * Builds a valid request used in tests where the server responds with success=true
 * but malformed `data` (e.g., missing items or invalid shape). The request itself is valid.
 *
 * @returns {ListEntriesRequest} Valid request for malformed JSON tests.
 */
export function ac03MalformedJson(): ListEntriesRequest {
    const payload = getPayload('UC-02', 'AC-03');

    return payload;
}

/**
 * AC-04 — Success=false request (contract guard).
 *
 * Builds a valid request used in tests where the API responds with { success:false }
 * together with HTTP 200 (logical failure branch). The request is correct; only outcome differs.
 *
 * @returns {ListEntriesRequest} Valid request for success=false tests.
 */
export function ac04SuccessFalse(): ListEntriesRequest {
    const payload = getPayload('UC-02', 'AC-04');

    return payload;
}
