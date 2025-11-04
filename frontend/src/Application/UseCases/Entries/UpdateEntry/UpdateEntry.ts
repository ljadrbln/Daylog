import type {EntryRepositoryInterface} from '@src/Domain/Interfaces/Entries/EntryRepositoryInterface';
import type {Entry} from '@src/Domain/Models/Entries/Entry';
import type {UpdateEntryRequest} from '@src/Application/DTO/Entries/UpdateEntry/UpdateEntryRequest';
import type {UseCaseResponse} from '@src/Application/UseCases/Shared/UseCaseResponse';

/**
 * UC-5: Update Entry — Application use case.
 *
 * Purpose:
 * Connects UpdateEntryRequest (id + fields) with the domain repository save()
 * and returns a typed UseCaseResponse<Entry>.
 *
 * Mechanics:
 * - Extract id, title, body, date from request.
 * - Build an entry-like object for repo.save().
 * - If repository returns an Entry → { success:true, status:200, data }.
 * - If repository returns null → { success:false, status:404, code:'ENTRY_NOT_FOUND' }.
 * - Validation errors (422) are handled by validators/Presentation, not here.
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
     * @returns {Promise<UseCaseResponse<Entry>>} Typed envelope for UI layer.
     */
    public async execute(request: UpdateEntryRequest): Promise<UseCaseResponse<Entry>> {
        const id = request.id;
        const title = request.title ?? '';
        const body = request.body ?? '';
        const date = request.date ?? '';

        const input: Entry = {
            id,
            title,
            body,
            date,
            createdAt: '',
            updatedAt: ''
        };

        const updated = await this.repo.save(input);

        if (updated) {
            const ok: UseCaseResponse<Entry> = {
                success: true,
                status: 200,
                data: updated
            };

            return ok;
        }

        const notFound: UseCaseResponse<Entry> = {
            success: false,
            status: 404,
            code: 'ENTRY_NOT_FOUND'
        };

        return notFound;
    }
}
