/**
 * Temporary stub for UC-2 ListEntries use case.
 *
 * Purpose:
 * Allow RED phase of the test to compile before implementation.
 * Throws explicitly to mark unimplemented logic.
 */
export class ListEntries {
    public constructor(_repo: unknown) {}

    public async execute(_request: unknown): Promise<never> {
        throw new Error('Not implemented yet');
    }
}
