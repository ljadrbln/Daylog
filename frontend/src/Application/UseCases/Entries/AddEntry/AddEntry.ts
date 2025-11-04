import type {EntryRepositoryInterface} from '@src/Domain/Interfaces/Entries/EntryRepositoryInterface';
import type {Entry} from '@src/Domain/Models/Entries/Entry';
import type {AddEntryRequest} from '@src/Application/DTO/Entries/AddEntry/AddEntryRequest';
import type {AddEntryResponse} from '@src/Application/DTO/Entries/AddEntry/AddEntryResponse';

/**
 * UC-1: Add Entry — Application use case (pure).
 *
 * Purpose:
 * Bridge Application DTO (title/body/date) with the domain repository save()
 * and return a typed AddEntryResponse on success. No exception handling here:
 * validation and transport concerns are handled by validators/Presentation.
 *
 * Mechanics:
 * - Extract fields from request.
 * - Call repo.save(entry-like object).
 * - Always return { success:true, status:200, data } on success.
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
     * @returns {Promise<AddEntryResponse>} Typed envelope for UI.
     */
    public async execute(request: AddEntryRequest): Promise<AddEntryResponse> {
        const title = request.title;
        const body = request.body;
        const date = request.date;

        const input: Entry = {
            id: '',
            title,
            body,
            date,
            createdAt: '',
            updatedAt: ''
        };

        const saved = await this.repo.save(input);

        const response: AddEntryResponse = {
            success: true,
            status: 200,
            data: saved
        };

        return response;
    }
}
