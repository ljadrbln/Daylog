import type {EntryRepositoryInterface} from '@src/Domain/Interfaces/Entries/EntryRepositoryInterface';
import type {Entry} from '@src/Domain/Models/Entries/Entry';
import type {UpdateEntryRequest} from '@src/Application/DTO/Entries/UpdateEntry/UpdateEntryRequest';
import type {UseCaseResponse} from '@src/Application/UseCases/Shared/UseCaseResponse';

/**
 * UC-5: Update Entry — Application use case.
 *
 * Purpose:
 * Bridge UpdateEntryRequest (id + optional fields) with repo.save() and return
 * typed UseCaseResponse<Entry> for success, or map 404/422 to failure envelopes.
 *
 * Mechanics:
 * - Extract id and optional fields into local variables.
 * - Build an "entry-like" object; backend merges provided fields and ignores placeholders.
 * - On success → { success:true, status:200, data }.
 * - If repo signals 404 → { success:false, status:404, code:'ENTRY_NOT_FOUND' }.
 * - If validation fails (422) → { success:false, status:422, code:'VALIDATION_FAILED' }.
 */
export class UpdateEntry {
    private readonly repo: EntryRepositoryInterface;

    /**
     * @param {EntryRepositoryInterface} repo Domain repository dependency (injected).
     */
    public constructor(repo: EntryRepositoryInterface) {
        this.repo = repo;
    }

    /**
     * Execute UC-5 with the given request.
     *
     * @param {UpdateEntryRequest} request DTO with id and optional fields.
     * @returns {Promise<UseCaseResponse<Entry>>} Typed envelope for UI.
     */
    public async execute(request: UpdateEntryRequest): Promise<UseCaseResponse<Entry>> {
        throw new Error('Update Entry not implemented');
    }
}
