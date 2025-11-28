/* eslint-disable no-unused-vars */

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
