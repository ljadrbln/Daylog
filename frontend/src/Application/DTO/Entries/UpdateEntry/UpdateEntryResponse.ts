// src/Application/DTO/Entries/UpdateEntry/UpdateEntryResponse.ts
import type {UseCaseResponse} from '@src/Application/DTO/Common/UseCaseResponse';
import type {Entry} from '@src/Domain/Entries/Entry';

/**
 * UC-5: Update Entry — response DTO.
 * Mirrors backend: { success:true, status:200, data: Entry } on success.
 */
export type UpdateEntryResponse = UseCaseResponse<Entry>;
