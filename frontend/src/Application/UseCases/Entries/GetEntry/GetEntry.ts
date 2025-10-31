import type {EntryRepositoryInterface} from '@src/Domain/Interfaces/Entries/EntryRepositoryInterface';
import type {GetEntryRequest} from '@src/Application/DTO/Entries/GetEntry/GetEntryRequest';
import type {GetEntryResponse} from '@src/Application/DTO/Entries/GetEntry/GetEntryResponse';

/**
 * UC-3: Get Entry — Application use case.
 *
 * Purpose:
 * Bridge Application DTO (id) with the domain EntryRepository and
 * return a typed UseCaseResponse envelope for both success and 404 branches.
 *
 * Mechanics:
 * - Extract id from request.
 * - Call repo.findById(id).
 * - If found → { success:true, status:200, data: entry }.
 * - If not found → { success:false, status:404, code:'ENTRY_NOT_FOUND' }.
 */
export class GetEntry {
    private readonly repo: EntryRepositoryInterface;

    /**
     * @param {EntryRepositoryInterface} repo Domain repository dependency (injected).
     */
    public constructor(repo: EntryRepositoryInterface) {
        this.repo = repo;
    }

    /**
     * Execute UC-3 with the given request.
     *
     * @param {GetEntryRequest} request DTO with the entry identifier.
     * @returns {Promise<GetEntryResponse>} Typed transport envelope for UI.
     */
    public async execute(request: GetEntryRequest): Promise<GetEntryResponse> {
        const id = request.id;
        const entry = await this.repo.findById(id);

        if (entry) {
            const response: GetEntryResponse = {
                success: true,
                status: 200,
                data: entry
            };

            return response;
        }

        const notFound: GetEntryResponse = {
            success: false,
            status: 404,
            code: 'ENTRY_NOT_FOUND'
        };

        return notFound;
    }
}
