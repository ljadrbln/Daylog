export type HttpMethod = 'GET' | 'POST' | 'PUT' | 'PATCH' | 'DELETE';

export interface HttpClient {
    request<T>(method: HttpMethod, url: string, init?: RequestInit): Promise<T>;
}

export class FetchHttpClient implements HttpClient {
    private readonly baseUrl: string;

    constructor(baseUrl: string) {
        // Sanitize url and remove trailing slash
        this.baseUrl = baseUrl.replace(/\/+$/, '');
    }

    async request<T>(method: HttpMethod, url: string, init: RequestInit = {}): Promise<T> {
        const fullUrl = url.startsWith('http') 
            ? url 
            : `${this.baseUrl}${url}`;
        
        const res = await fetch(fullUrl, {
            method,
            headers: { 
                Accept: 'application/json', 
                ...(init.headers ?? {}) 
            },
            body: init.body,
        });

        const text = await res.text();
        const data = text ? JSON.parse(text) : undefined;

        if (!res.ok) {
            const message = `HTTP ${res.status} for ${fullUrl}`;
            throw new Error(message);
        }

        return data as T;
    }
}
