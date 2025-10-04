/* eslint-disable no-unused-vars */

export type HttpMethod = 'GET' | 'POST' | 'PUT' | 'PATCH' | 'DELETE';

export interface HttpClient {
    request<T>(method: HttpMethod, url: string, init?: RequestInit): Promise<T>;
}
