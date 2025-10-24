import type {EntryRepositoryInterface} from '@src/Domain/Interfaces/Entries/EntryRepositoryInterface';
import type {ListEntriesRequest} from '@src/Application/DTO/Entries/ListEntries/ListEntriesRequest';
import type {ListEntriesResponse} from '@src/Application/DTO/Entries/ListEntries/ListEntriesResponse';
import {ListEntriesCriteriaFactory} from '@src/Application/Factories/ListEntriesCriteriaFactory';

/**
 * UC-2: List Entries — Application use case.
 *
 * Purpose:
 * Bridge Application DTOs with the domain EntryRepository and
 * return typed UseCaseResponse envelope on the happy path.
 *
 * Mechanics:
 * - Build criteria via ListEntriesCriteriaFactory.fromRequest(request).
 * - Delegate to repo.list(criteria).
 * - Return { success:true, status:200, data: page }.
 */
export class ListEntries {
    private readonly repo: EntryRepositoryInterface;

    /**
     * @param {EntryRepositoryInterface} repo Domain repository dependency (injected).
     */
    public constructor(repo: EntryRepositoryInterface) {
        this.repo = repo;
    }

    /**
     * Execute UC-2 with the given request.
     *
     * @param {ListEntriesRequest} request
     * @returns {Promise<ListEntriesResponse>} Success envelope with page data.
     */
    public async execute(request: ListEntriesRequest): Promise<ListEntriesResponse> {
        const criteria = ListEntriesCriteriaFactory.fromRequest(request);
        const page = await this.repo.list(criteria);

        const response: ListEntriesResponse = {
            success: true,
            status: 200,
            data: page
        };

        return response;
    }
}
