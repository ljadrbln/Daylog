import type {EntryRepositoryInterface} from '@src/Domain/Interfaces/Entries/EntryRepositoryInterface';
import type {Entry} from '@src/Domain/Models/Entries/Entry';
import type {AddEntryRequest} from '@src/Application/DTO/Entries/AddEntry/AddEntryRequest';
import type {UseCaseResponse} from '@src/Application/UseCases/Shared/UseCaseResponse';

/**
 * UC-1: Add Entry — Application use case.
 *
 * Purpose:
 * Bridge DTO (title/body/date) with domain repository save() and produce a typed
 * UseCaseResponse<Entry> on success or a validation failure envelope on error.
 *
 * Mechanics:
 * - Extract fields from request into explicit local variables.
 * - Build an input "entry-like" object for repo.save(). Backend ignores id/timestamps on create.
 * - Return { success:true, status:200, data } on success.
 * - On domain validation failure (422) → { success:false, status:422, code:'VALIDATION_FAILED' }.
 *
 * @template Entry
 */
export class AddEntry {
    private readonly repo: EntryRepositoryInterface;

    /**
     * @param {EntryRepositoryInterface} repo Domain repository dependency (injected).
     */
    public constructor(repo: EntryRepositoryInterface) {
        this.repo = repo;
    }

    /**
     * Execute UC-1 with the given request.
     *
     * @param {AddEntryRequest} request DTO with title/body/date.
     * @returns {Promise<UseCaseResponse<Entry>>} Typed response envelope for the UI.
     */
    public async execute(request: AddEntryRequest): Promise<UseCaseResponse<Entry>> {
        throw new Error('Add entry not implemented');
    }
}
