/* eslint-disable no-unused-vars */

/**
 * Enumerates all HTTP methods supported by the transport layer.
 *
 * Usage:
 * - Defines allowed verbs for FetchHttpClient.request().
 * - Keeps signatures type-safe at call sites.
 */
export type HttpMethod = 'GET' | 'POST' | 'PUT' | 'PATCH' | 'DELETE';

/**
 * Transport options for HttpClient.
 *
 * Purpose:
 * - Extend native RequestInit from fetch().
 * - Replace the standard `body` with flexible `requestBody` that accepts any JSON-serializable object.
 * - The implementation (e.g., FetchHttpClient) is responsible for JSON.stringify.
 *
 * Typical use:
 * ```ts
 * this.http.request('POST', '/api/entries', {requestBody: entry});
 * ```
 */
export interface RequestOptions extends Omit<RequestInit, 'body'> {
    requestBody?: unknown;
}

/**
 * Base interface for all HTTP clients used in Infrastructure layer.
 *
 * Contract:
 * - Executes HTTP requests with JSON serialization of requestBody (if present).
 * - Always returns a parsed JSON object typed as <T>.
 *
 * Implementations:
 * - FetchHttpClient — default production client wrapping the native fetch().
 */
export interface HttpClient {
    request<T>(method: HttpMethod, url: string, options?: RequestOptions): Promise<T>;
}
