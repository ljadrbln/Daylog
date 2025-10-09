import {expect} from 'vitest';

/**
 * Ensures the response is the success branch and returns the same object
 * with a narrowed type, so `response.data` is safely available.
 *
 * If not success, the test fails via expect(...).
 */
export function ensureSuccess<T extends {success: boolean}>(r: T): T & {success: true} {
    expect(r.success).toBe(true);

    const ok = r as T & {success: true};

    return ok;
}
