/**
 * Generic interface for any Application-level use case.
 *
 * @template TRequest Request DTO type.
 */
export interface ExecutableUseCase<TRequest> {
    /**
     * Execute the use case with given request DTO.
     *
     * @param {TRequest} req
     * Request DTO.
     *
     * @returns {Promise<unknown>}
     * Promise resolving to raw Application result or throwing an error.
     */
    execute(req: TRequest): Promise<unknown>;
}

/**
 * Success envelope returned by UseCaseRunner.
 *
 * @template TData Payload type of successful response.
 */
export type RunnerSuccess<TData> = {
    success: true;
    status: 200;
    data: TData;
};

/**
 * Failure envelope returned by UseCaseRunner.
 */
export type RunnerFailure = {
    success: false;
    status: number;
    code: string;
    errors?: unknown;
};

/**
 * UseCaseRunner — Presentation-level wrapper for executing Application use cases.
 *
 * Purpose:
 * Execute any Application use case and normalize its output for UI consumption:
 * - Success → { success:true, status:200, data }
 * - Failure → { success:false, status, code, errors? }
 */
export const UseCaseRunner = {
    /**
     * Execute a given use case with request and normalize the result for UI.
     *
     * @template TRequest Incoming request DTO type.
     * @template TData    Data type expected in the success envelope.
     *
     * @param {ExecutableUseCase<TRequest>} uc
     * Use case exposing an async execute(req) method.
     *
     * @param {TRequest} request
     * Transport-level request DTO to pass into the use case.
     *
     * @returns {Promise<RunnerSuccess<TData> | RunnerFailure>}
     * A UI-friendly envelope with either success payload or normalized error.
     */
    async run<TRequest, TData>(
        uc: ExecutableUseCase<TRequest>,
        request: TRequest
    ): Promise<RunnerSuccess<TData> | RunnerFailure> {
        try {
            const req = request;
            const result = await uc.execute(req);

            const asAny = result as {data?: TData};
            const extractedData = asAny.data as TData;

            const envelope: RunnerSuccess<TData> = {
                success: true,
                status: 200,
                data: extractedData
            };

            return envelope;
        } catch (err: unknown) {
            const thrown = err as {status?: number; code?: string; errors?: unknown};

            const status = typeof thrown.status === 'number' ? thrown.status : 500;
            const errors = thrown.errors;

            let code = thrown.code;
            if (!code) {
                if (status === 404) {
                    code = 'ENTRY_NOT_FOUND';
                } else if (status === 422) {
                    code = 'VALIDATION_ERROR';
                } else {
                    code = 'UNKNOWN_ERROR';
                }
            }

            const failure: RunnerFailure = {
                success: false,
                status,
                code,
                errors
            };

            return failure;
        }
    }
};
