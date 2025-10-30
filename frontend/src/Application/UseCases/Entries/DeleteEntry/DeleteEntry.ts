// src/Application/UseCases/Entries/DeleteEntry/DeleteEntry.ts

import type {EntryRepositoryInterface} from '@src/Domain/Interfaces/Entries/EntryRepositoryInterface';
import type {DeleteEntryRequest} from '@src/Application/DTO/Entries/DeleteEntry/DeleteEntryRequest';
import type {DeleteEntryResponse} from '@src/Application/DTO/Entries/DeleteEntry/DeleteEntryResponse';

/**
 * UC-4: Delete Entry — Application use case.
 *
 * Purpose:
 * Bridge Application DTO (id) with the domain EntryRepository.deleteById and
 * return a typed UseCaseResponse envelope for both success and 404 branches.
 *
 * Mechanics:
 * - Extract id from request.
 * - Call repo.deleteById(id).
 * - If deleted → { success:true, status:200, data: entry }.
 * - If repository signals not-found → { success:false, status:404, code:'ENTRY_NOT_FOUND' }.
 */
export class DeleteEntry {
    private readonly repo: EntryRepositoryInterface;

    /**
     * @param {EntryRepositoryInterface} repo Domain repository dependency (injected).
     */
    public constructor(repo: EntryRepositoryInterface) {
        this.repo = repo;
    }

    /**
     * Execute UC-4 with the given request.
     *
     * @param {DeleteEntryRequest} request DTO with the entry identifier.
     * @returns {Promise<DeleteEntryResponse>} Typed transport envelope for UI.
     */
    public async execute(request: DeleteEntryRequest): Promise<DeleteEntryResponse> {
        const id = request.id;
        const entry = await this.repo.deleteById(id);

        if (entry) {
            const response: DeleteEntryResponse = {
                success: true,
                status: 200,
                data: entry
            };

            return response;
        }

        const notFound: DeleteEntryResponse = {
            success: false,
            status: 404,
            code: 'ENTRY_NOT_FOUND'
        };

        return notFound;
    }
}
