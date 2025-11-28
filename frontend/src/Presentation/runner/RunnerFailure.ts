/**
 * Failure envelope returned by UseCaseRunner.
 */
export type RunnerFailure = {
    success: false;
    status: number;
    code: string;
    errors?: unknown;
};
