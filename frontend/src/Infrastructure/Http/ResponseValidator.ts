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
     * Validates envelope and returns its data if well-formed.
     *
     * @throws {Error} when success !== true or data is missing/malformed.
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
