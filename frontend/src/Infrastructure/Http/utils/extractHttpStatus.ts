/**
 * Extract numeric HTTP status from an unknown error object.
 *
 * Purpose:
 * Safely read `.status` when HttpClient throws a transport-level error.
 * Returns `undefined` if status is missing, malformed, or not numeric.
 *
 * @param error unknown Any thrown value (HttpError or arbitrary object)
 * @returns number|undefined Numeric HTTP status, if present and valid.
 */
export function extractHttpStatus(error: unknown): number | undefined {
    if (typeof error !== 'object' || error === null) {
        return undefined;
    }

    if (!('status' in error)) {
        return undefined;
    }

    const value = (error as Record<string, unknown>).status;

    // prettier-ignore
    return typeof value === 'number'
        ? value
        : undefined;
}
