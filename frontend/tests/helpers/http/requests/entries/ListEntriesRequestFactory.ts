// UC-2 ListEntries — Request factory (AC01–AC04).
// Returns plain request objects without importing types from src.

/**
 * AC-01 — Happy Path request.
 *
 * Builds a valid query with explicit pagination and sorting.
 * Mirrors a typical client request to fetch the latest entries by updatedAt.
 *
 * @returns {{
 *   query?: string,
 *   page?: number,
 *   perPage?: number,
 *   sortField?: 'date'|'updatedAt',
 *   sortDir?: 'ASC'|'DESC',
 *   dateFrom?: string,
 *   dateTo?: string
 * }} Valid ListEntries request.
 */
export function ac01HappyPath(): {
    query?: string;
    page?: number;
    perPage?: number;
    sortField?: 'date' | 'updatedAt';
    sortDir?: 'ASC' | 'DESC';
    dateFrom?: string;
    dateTo?: string;
} {
    // prettier-ignore
    {
        const query     = 'hello';
        const page      = 1;
        const perPage   = 10;
        const sortField = 'updatedAt' as const;
        const sortDir   = 'DESC' as const;

        const payload = {query, page, perPage, sortField, sortDir};

        return payload;
    }
}

/**
 * AC-02 — Non-2xx Response request.
 *
 * Builds a syntactically valid request intended for transport-level error tests
 * (e.g., 400/500). The request itself is correct; only HTTP status differs.
 */
export function ac02Non2xxResponse() {
    // prettier-ignore
    {
        const query     = 'status-check';
        const page      = 1;
        const perPage   = 10;
        const sortField = 'date' as const;
        const sortDir   = 'DESC' as const;

        const payload = {query, page, perPage, sortField, sortDir};

        return payload;
    }
}

/**
 * AC-03 — Malformed JSON request.
 *
 * Builds a valid request used in tests where the server responds with
 * success=true but malformed `data` (e.g., missing items or wrong shapes).
 */
export function ac03MalformedJson() {
    // prettier-ignore
    {
        const query     = 'malformed';
        const page      = 1;
        const perPage   = 10;
        const sortField = 'updatedAt' as const;
        const sortDir   = 'DESC' as const;

        const payload = {query, page, perPage, sortField, sortDir};

        return payload;
    }
}

/**
 * AC-04 — success=false request.
 *
 * Builds a valid request used in tests where the API responds with
 * { success:false } and HTTP status 200 (logical failure branch).
 */
export function ac04SuccessFalse() {
    // prettier-ignore
    {
        const query     = 'logical-failure';
        const page      = 1;
        const perPage   = 10;
        const sortField = 'updatedAt' as const;
        const sortDir   = 'DESC' as const;

        const payload = {query, page, perPage, sortField, sortDir};

        return payload;
    }
}
