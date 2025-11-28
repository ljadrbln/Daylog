import {FetchHttpClient} from '@src/Infrastructure/Http/FetchHttpClient';
import {EntryRepository} from '@src/Infrastructure/Repositories/Entries/EntryRepository';
import {AddEntry} from '@src/Application/UseCases/Entries/AddEntry/AddEntry';

/**
 * Factory for UC-1 (AddEntry) wiring.
 *
 * Purpose:
 * - Provide a fully constructed AddEntry use case for the Presentation layer.
 * - Hide infrastructure details (HTTP client, repository).
 *
 * Notes:
 * - No business logic here — only composition.
 * - Part of the frontend Composition Root for Entry-related use cases.
 */
export function makeAddEntryUseCase(): AddEntry {
    // 0) Read base URL
    const baseUrl = import.meta.env.VITE_API_BASE_URL;

    // 1) Infrastructure: HTTP client
    const httpClient = new FetchHttpClient(baseUrl);

    // 2) Infrastructure: Repository
    const repo = new EntryRepository(httpClient);

    // 3) Application: Use Case
    const useCase = new AddEntry(repo);

    return useCase;
}
