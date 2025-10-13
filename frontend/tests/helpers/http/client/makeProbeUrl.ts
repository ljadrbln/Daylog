/**
 * Builds a probe URL for HttpClient tests (transport-only, no DTOs).
 *
 * @param {string} uc Label for grouping (e.g., "HTTP").
 * @param {string} ac Label for case (e.g., "NetworkFailure").
 * @returns {string} Relative URL used by HttpClient in tests.
 */
export function makeProbeUrl(uc: string, ac: string): string {
    const url = `/api/probe?uc=${uc}&ac=${ac}`;

    return url;
}
