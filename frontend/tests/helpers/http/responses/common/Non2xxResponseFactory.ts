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
 * Shape of a generic transport-level error payload.
 */
export interface TransportErrorResponse {
    success: false;
    status: number;
    message: string;
}

/**
 * Builds a generic transport error payload.
 *
 * @param {number} status HTTP status code (non-2xx)
 * @param {string} [message='Transport error'] Optional textual description.
 * @returns {TransportErrorResponse} Generic transport-level error payload.
 */
function transportError(status: number, message: string = 'Transport error'): TransportErrorResponse {
    // prettier-ignore
    const payload: TransportErrorResponse = {
        success: false,
        status : status,
        message: message
    };

    return payload;
}

/**
 * 400 Bad Request (generic).
 */
export function badRequest(): TransportErrorResponse {
    return transportError(400, 'Bad Request');
}

/**
 * 500 Internal Server Error.
 */
export function internalServerError(): TransportErrorResponse {
    return transportError(500, 'Internal Server Error');
}

/**
 * 503 Service Unavailable.
 */
export function serviceUnavailable(): TransportErrorResponse {
    return transportError(503, 'Service Unavailable');
}
