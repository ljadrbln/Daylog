
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
