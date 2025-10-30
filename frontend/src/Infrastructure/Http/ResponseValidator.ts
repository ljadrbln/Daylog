import type {UseCaseResponse} from '@src/Application/DTO/Common/UseCaseResponse';

/**
 * ResponseValidator
 *
 * Purpose:
 * Centralizes transport-level validation of UseCaseResponse envelopes.
 * Ensures responses have success=true and non-null data object.
 */
export class ResponseValidator {
    /**
     * Ensure success branch; ignore data.
     *
     * @param {UseCaseResponse<unknown>} json Envelope to validate.
     * @param {string} endpoint Human-friendly endpoint label for error message.
     * @returns {void}
     * @throws {Error} If success !== true.
     */
    public static ensureSuccess(json: UseCaseResponse<unknown>, endpoint: string): void {
        const ok = json.success === true;

        if (!ok) {
            const message = `Malformed response for ${endpoint}`;
            throw new Error(message);
        }
    }

    /**
     * Extracts typed `data` field from a successful UseCaseResponse.
     *
     * - On {success:true, data:T} → returns data.
     * - On {success:false, status:404} → returns null (for UC consistency).
     * - On malformed or unexpected → throws Error.
     */
    public static extractData<T>(json: UseCaseResponse<T>, endpoint: string): T | null {
        // Happy path: normal success
        if (json.success === true && typeof json.data === 'object' && json.data !== null) {
            return json.data as T;
        }

        // Domain-level "not found" → interpret as null instead of throwing
        if (json.success === false && json.status === 404 && json.code === 'ENTRY_NOT_FOUND') {
            return null;
        }

        // Everything else (422, 400, invalid shape) → true error
        const message = `Malformed response for ${endpoint}`;
        throw new Error(message);
    }
}
