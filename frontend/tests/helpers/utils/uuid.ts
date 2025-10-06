/**
 * Simple UUID v4 generator for test data.
 *
 * Purpose:
 * Produce pseudo-random UUIDs using only built-in JS functions.
 * Not cryptographically secure, but sufficient for test fixtures.
 *
 * @returns {string} UUID v4 string, e.g. "3f6f38d2-9a2a-4c9b-b821-7a5a1b93a3d4".
 */
export function uuidv4(): string {
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, (c) => {
        const r = (Math.random() * 16) | 0;
        const v = c === 'x' ? r : (r & 0x3) | 0x8;
        return v.toString(16);
    });
}
