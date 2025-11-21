import {FetchHttpClient} from '@src/Infrastructure/Http/FetchHttpClient';
import {EntryRepository} from '@src/Infrastructure/Repositories/Entries/EntryRepository';
import {ListEntries} from '@src/Application/UseCases/Entries/ListEntries/ListEntries';

/**
 * Factory for UC-2 (ListEntries) wiring.
 *
 * Purpose:
 * - Provide a fully constructed ListEntries use case for the Presentation layer.
 * - Hide infrastructure details (HTTP client, repository).
 *
 * Notes:
 * - No business logic here — only composition.
 * - This is the first step toward a full frontend Composition Root.
 */
export function makeListEntriesUseCase(): ListEntries {
    // 0) Read base URL
    const baseUrl = import.meta.env.VITE_API_BASE_URL;

    // 1) Infrastructure: HTTP client
    const httpClient = new FetchHttpClient(baseUrl);

    // 2) Infrastructure: Repository
    const repo = new EntryRepository(httpClient);

    // 3) Application: Use Case
    const useCase = new ListEntries(repo);

    return useCase;
}
