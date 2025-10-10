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

/**
 * AC-02: any valid UUID; value is irrelevant for non-2xx checks.
 */
export function ac02Non2xxResponse(): DeleteEntryRequest {
    return {
        id: 'aaaaaaaa-aaaa-4aaa-8aaa-aaaaaaaaaaaa'
    };
}

/**
 * AC-03 — Malformed JSON (success:true but no data)
 * Uses a stable UUID; value irrelevant for this test.
 */
export function ac03MalformedJson(): DeleteEntryRequest {
    return {
        id: 'bbbbbbbb-bbbb-4bbb-8bbb-bbbbbbbbbbbb'
    };
}

/**
 * AC-04 — Contract guard (success:false with 200).
 * Uses a stable UUID; concrete value is irrelevant for this check.
 */
export function ac04SuccessFalse(): DeleteEntryRequest {
    return {
        id: 'cccccccc-cccc-4ccc-8ccc-cccccccccccc'
    };
}
