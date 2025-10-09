import type {HttpClient} from '@src/Infrastructure/Http/HttpClient';
import type {Entry} from '@src/Domain/Entries/Entry';
import type {ListEntriesRequest} from '@src/Application/DTO/Entries/ListEntries/ListEntriesRequest';
import type {
    ListEntriesData,
    ListEntriesResponse
} from '@src/Application/DTO/Entries/ListEntries/ListEntriesResponse';

/**
 * UC-2: List Entries (Frontend)
 *
 * Fetches a paginated list of entries from GET /api/entries with query params.
 *
 * Transport-level validation:
 * - HTTP must be ok (delegated to HttpClient);
 * - response.success must be true;
 * - response.data must be a non-null object;
 * - response.data.items must be an array;
 * - pagination scalars (page, perPage, total, pagesCount) must be numbers.
 *
 * Field-level validation of Entry is out of scope (handled by backend/higher layers).
 */
export class ListEntriesGateway {
    private readonly http: HttpClient;

    constructor(http: HttpClient) {
        this.http = http;
    }

    /**
     * UC-2: List Entries (Gateway)
     *
     * Performs GET /api/entries with serialized query string built from ListEntriesRequest.
     * Returns the `data` object (items + pagination) from the backend envelope.
     *
     * @param {ListEntriesRequest} req Query parameters to filter/sort/paginate entries.
     * @returns {Promise<ListEntriesData>} Items with pagination as provided by backend.
     * @throws {Error} If HTTP is non-2xx or the transport envelope is malformed.
     */
    async list(req: ListEntriesRequest = {}): Promise<ListEntriesData> {
        const url = this.buildUrl('/api/entries', req);

        const json = await this.http.request<ListEntriesResponse>('GET', url);

        if (json.success !== true) {
            const message = 'Malformed response for GET /api/entries';
            throw new Error(message);
        }

        const dataOk = typeof json.data === 'object' && json.data !== null;
        if (!dataOk) {
            const message = 'Malformed response for GET /api/entries';
            throw new Error(message);
        }

        const {items, page, perPage, total, pagesCount} = json.data as ListEntriesData;

        const itemsIsArray = Array.isArray(items);
        const scalarsOk =
            typeof page === 'number' &&
            typeof perPage === 'number' &&
            typeof total === 'number' &&
            typeof pagesCount === 'number';

        if (!itemsIsArray || !scalarsOk) {
            const message = 'Malformed response for GET /api/entries';
            throw new Error(message);
        }

        const result: ListEntriesData = {
            items: items as Entry[],
            page,
            perPage,
            total,
            pagesCount
        };

        return result;
    }

    /**
     * Serializes ListEntriesRequest into a query string.
     * - `query` may be string or string[] (maps to ?query=... or ?query[]=...).
     * - Omits undefined fields.
     * - Uses strict key names matching backend contract.
     *
     * @param {string} base Base path (/api/entries).
     * @param {ListEntriesRequest} req Request with optional filters/sort/pagination.
     * @returns {string} Fully composed URL with query string, or base if empty.
     */
    private buildUrl(base: string, req: ListEntriesRequest): string {
        const params = new URLSearchParams();

        if (typeof req.query === 'string') {
            params.append('query', req.query);
        }

        if (typeof req.page === 'number') {
            params.append('page', String(req.page));
        }
        if (typeof req.perPage === 'number') {
            params.append('perPage', String(req.perPage));
        }
        if (typeof req.sortField === 'string') {
            params.append('sortField', req.sortField);
        }
        if (typeof req.sortDir === 'string') {
            params.append('sortDir', req.sortDir);
        }
        if (typeof req.dateFrom === 'string') {
            params.append('dateFrom', req.dateFrom);
        }
        if (typeof req.dateTo === 'string') {
            params.append('dateTo', req.dateTo);
        }

        const query = params.toString();
        const url = query ? `${base}?${query}` : base;

        return url;
    }
}
