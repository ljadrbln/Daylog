import type {EntryRepositoryInterface} from '@src/Domain/Interfaces/Entries/EntryRepositoryInterface';
import type {Entry} from '@src/Domain/Models/Entries/Entry';
import type {UpdateEntryRequest} from '@src/Application/DTO/Entries/UpdateEntry/UpdateEntryRequest';
import type {UpdateEntryResponse} from '@src/Application/DTO/Entries/UpdateEntry/UpdateEntryResponse';

/**
 * UC-5: Update Entry — Application use case (thin).
 *
 * Purpose:
 * Bridge Application DTO with the domain repository save() and return
 * a typed UseCaseResponse on success. No exception handling here —
 * 404/422/transport errors are propagated to Presentation.
 *
 * Mechanics:
 * - Extract id/title/body/date from request (id is required).
 * - Call repo.save(entry).
 * - Return { success:true, status:200, data } on success.
 *
 * @returns Promise<UpdateEntryResponse>
 */
export class UpdateEntry {
    private readonly repo: EntryRepositoryInterface;

    /**
     * @param {EntryRepositoryInterface} repo Domain repository dependency.
     */
    public constructor(repo: EntryRepositoryInterface) {
        this.repo = repo;
    }

    /**
     * Execute UC-5 with the given request.
     *
     * @param {UpdateEntryRequest} request DTO with id and mutable fields.
     * @returns {Promise<UpdateEntryResponse>} Typed envelope for UI.
     */
    public async execute(request: UpdateEntryRequest): Promise<UpdateEntryResponse> {
        const id    = request.id;
        const title = request.title ?? '';
        const body  = request.body ?? '';
        const date  = request.date ?? '';

        const input: Entry = {
            id,
            title,
            body,
            date,
            createdAt: '',
            updatedAt: ''
        };

        const saved = await this.repo.save(input);

        const response: UpdateEntryResponse = {
            success: true,
            status: 200,
            data: saved
        };

        return response;
    }
}
