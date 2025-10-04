import type {HttpClient, HttpMethod} from './HttpClient';

export class FetchHttpClient implements HttpClient {
    private readonly baseUrl: string;

    constructor(baseUrl: string) {
        // Sanitize url and remove trailing slash
        this.baseUrl = baseUrl.replace(/\/+$/, '');
    }

    async request<T>(method: HttpMethod, url: string, init: RequestInit = {}): Promise<T> {
        // prettier-ignore
        const fullUrl = url.startsWith('http') 
            ? url 
            : `${this.baseUrl}${url}`;

        const res = await fetch(fullUrl, {
            method,
            headers: {
                Accept: 'application/json',
                ...(init.headers ?? {})
            },
            body: init.body
        });

        if (!res.ok) {
            const message = `HTTP ${res.status} for ${fullUrl}`;
            throw new Error(message);
        }

        const text = await res.text();

        // prettier-ignore
        const data = text 
            ? JSON.parse(text) 
            : undefined;

        return data as T;
    }
}
