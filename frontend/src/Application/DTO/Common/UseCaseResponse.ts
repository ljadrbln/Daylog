/**
 * Transport-level envelope returned by backend use-cases.
 *
 * UI/Gateways only trust the fields they explicitly use:
 * - success: boolean flag of server-side result,
 * - data: optional payload with a shape specific to a use-case,
 * - status/code: optional diagnostics (ignored by UI unless explicitly needed).
 *
 * @template T Data payload shape for a specific use-case.
 */
export interface UseCaseResponse<T> {
    success: boolean;
    data?: T;
    status?: number;
    code?: string;
}
