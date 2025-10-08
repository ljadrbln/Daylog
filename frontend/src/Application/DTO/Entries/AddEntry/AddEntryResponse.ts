import type {Entry} from '@src/Domain/Entries/Entry';
import type {UseCaseResponse} from '@src/Application/DTO/Common/UseCaseResponse';

/**
 * UC-1: Add Entry — response DTO.
 *
 * Mirrors the backend payload:
 * {
 *   success: true,
 *   data: Entry,
 *   status: 200
 * }
 */
export interface AddEntryResponse extends UseCaseResponse<Entry> {}
