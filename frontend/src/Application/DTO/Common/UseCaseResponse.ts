export type UseCaseResponse<TData> = {
    success: boolean;
    data?: TData;
    status: number;
    code?: string;
    errors?: unknown;
};
