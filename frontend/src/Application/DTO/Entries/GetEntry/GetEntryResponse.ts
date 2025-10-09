import type {Entry} from '@src/Domain/Entries/Entry';
import type {UseCaseResponse} from '@src/Application/DTO/Common/UseCaseResponse';

/**
 * UC-3: Get Entry — response DTO.
 *
 * Mirrors backend payload exactly:
 * { success: true, status: 200, data: Entry }.
 *
 * Logical errors (e.g., ENTRY_NOT_FOUND) return { success:false, status:404, code:"..." }.
 */
export type GetEntryResponse = UseCaseResponse<Entry>;
