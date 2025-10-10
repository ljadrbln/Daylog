import type {Entry} from '@src/Domain/Entries/Entry';
import type {UseCaseResponse} from '@src/Application/DTO/Common/UseCaseResponse';

/**
 * UC-4: Delete Entry — response DTO.
 *
 * Mirrors backend payload exactly:
 * { success: true, status: 200, data: Entry }.
 *
 * Errors are returned with non-2xx HTTP status (e.g., 404/422) and success:false.
 */
export type DeleteEntryResponse = UseCaseResponse<Entry>;
