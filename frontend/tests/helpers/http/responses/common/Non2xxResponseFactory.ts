/**
 * Common response builders for transport-level (non-2xx) HTTP errors.
 *
 * Purpose:
 * Provide shared JSON payloads for gateway tests simulating network/server failures.
 *
 * These responses are NOT UC-specific and do not contain business error codes.
 * They represent generic HTTP-layer failures (400, 500, 503, etc.).
 */

/**
 * Builds a generic transport error payload.
 *
 * @param status - HTTP status code (non-2xx)
 * @param message - optional textual description
 * @returns object matching the expected error JSON schema
 */
export function makeTransportError(status: number, message: string = 'Transport error') {
    //prettier-ignore
    const payload = {
        success: false,
        status : status,
        message: message
    };

    return payload;
}

/**
 * 400 Bad Request (generic)
 *
 * Use when backend responds with a syntactically invalid request
 * or malformed payload not tied to a domain rule.
 */
export function makeBadRequest() {
    //prettier-ignore
    const status  = 400;
    const message = 'Bad Request';
    const payload = makeTransportError(status, message);

    return payload;
}

/**
 * 500 Internal Server Error
 *
 * Use when backend fails internally or returns an unexpected exception.
 */
export function makeInternalError() {
    //prettier-ignore
    const status  = 500;
    const message = 'Internal Server Error';
    const payload = makeTransportError(status, message);

    return payload;
}

/**
 * 503 Service Unavailable
 *
 * Use when the backend or network layer is temporarily unavailable.
 */
export function makeServiceUnavailable() {
    //prettier-ignore
    const status  = 503;
    const message = 'Service Unavailable';
    const payload = makeTransportError(status, message);

    return payload;
}
