import type {DeleteEntryRequest} from '@src/Application/DTO/Entries/DeleteEntry/DeleteEntryRequest';
import {uuidv4} from '@tests/helpers/utils/uuid';

/**
 * Request factory for UC-4 DeleteEntry.
 * AC-01 builds a valid payload with a stable UUID (no literals at call sites).
 */
export function ac01HappyPath(): DeleteEntryRequest {
    return {
        id: uuidv4()
    };
}
