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
     * Return typed data for success branch; require non-null object.
     *
     * @template T
     * @param {UseCaseResponse<T>} json Envelope to validate.
     * @param {string} endpoint Human-friendly endpoint label for error message.
     * @returns {T} Validated data payload.
     * @throws {Error} If success !== true or data is missing/malformed.
     */
    public static extractData<T>(json: UseCaseResponse<T>, endpoint: string): T {
        const isValid =
            json.success === true && typeof json.data === 'object' && json.data !== null;

        if (!isValid) {
            const message = `Malformed response for ${endpoint}`;
            throw new Error(message);
        }

        return json.data as T;
    }
}
