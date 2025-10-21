import type {HttpClient, HttpMethod, RequestOptions} from './HttpClient';

/**
 * Default implementation of HttpClient using native fetch().
 *
 * Mechanics:
 * - Joins baseUrl and relative paths.
 * - Serializes `requestBody` (if present) to JSON.
 * - Throws on non-2xx responses with clear message.
 * - Always returns parsed JSON typed as <T>.
 */
export class FetchHttpClient implements HttpClient {
    private readonly baseUrl: string;

    constructor(baseUrl: string) {
        /**
         * Sanitize base URL by removing trailing slashes.
         * Keeps concatenation stable for relative paths.
         */
        this.baseUrl = baseUrl.replace(/\/+$/, '');
    }

    /**
     * Execute HTTP request.
     *
     * @template T Expected response type (parsed JSON).
     * @param {HttpMethod} method HTTP verb (GET, POST, PATCH, etc.)
     * @param {string} url Absolute or relative URL.
     * @param {RequestOptions} options Transport options with optional requestBody.
     * @returns {Promise<T>} Parsed JSON response.
     */
    public async request<T>(
        method: HttpMethod,
        url: string,
        options: RequestOptions = {}
    ): Promise<T> {
        const fullUrl = url.startsWith('http') ? url : `${this.baseUrl}${url}`;

        const headers = {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            ...(options.headers ?? {})
        };

        const body = options.requestBody ? JSON.stringify(options.requestBody) : undefined;

        const response = await fetch(fullUrl, {
            method,
            headers,
            body,
            signal: options.signal
        });

        if (!response.ok) {
            const message = `HTTP ${response.status} for ${fullUrl}`;
            throw new Error(message);
        }

        const text = await response.text();

        const data = text ? (JSON.parse(text) as T) : (undefined as T);

        return data;
    }
}
