// frontend/src/Presentation/runner/UseCaseRunner.ts

/**
 * UseCaseRunner — Presentation-level wrapper for executing Application use cases.
 *
 * Purpose:
 * Provide a single, UI-friendly envelope for all use cases. It runs an arbitrary
 * Application use case (uc.execute(request)) and normalizes the outcome:
 * - Success → { success:true, status:200, data }
 * - Failure → { success:false, status, code, errors? }
 *
 * Mechanics:
 * - Accepts any object exposing execute(req): Promise<unknown>.
 * - On success: extracts `data` from the resolved payload and returns the success envelope.
 * - On error: extracts { status, code, errors } from the thrown object and applies
 *   fallback mapping for `code` if missing: 404→ENTRY_NOT_FOUND, 422→VALIDATION_ERROR,
 *   otherwise UNKNOWN_ERROR.
 * - No chained expressions: all intermediate values are assigned to variables
 *   before awaiting or returning (project convention).
 *
 * Cases:
 * - Happy path (success) — returns the normalized success envelope.
 * - 404/422/other errors — will be covered by dedicated tests AC02–AC04.
 */
export const UseCaseRunner = {
    /**
     * Execute a given use case with request and normalize the result for UI.
     *
     * @template TRequest Incoming request DTO type.
     * @template TData    Data type expected in the success envelope.
     *
     * @param {{ execute(req: TRequest): Promise<unknown> }} uc
     * Application use case exposing an async execute(req) method.
     *
     * @param {TRequest} request
     * Transport-level request DTO to pass into the use case.
     *
     * @returns {Promise<
     *   | { success: true;  status: 200; data: TData }
     *   | { success: false; status: number; code: string; errors?: unknown }
     * >}
     * A UI-friendly envelope with either success payload or normalized error.
     */
    async run<TRequest, TData>(
        uc: {execute(req: TRequest): Promise<unknown>},
        request: TRequest
    ): Promise<
        | {success: true; status: 200; data: TData}
        | {success: false; status: number; code: string; errors?: unknown}
    > {
        throw new Error('Not implemented');
    }
};
