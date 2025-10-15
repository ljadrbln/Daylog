import {describe, it, expect} from 'vitest';
import {ResponseValidator} from '@src/Infrastructure/Http/ResponseValidator';
import type {UseCaseResponse} from '@src/Application/DTO/Common/UseCaseResponse';

describe('ResponseValidator.extractData', () => {
    it('returns data when success=true and data is object', () => {
        // Arrange
        const json: UseCaseResponse<{a: number}> = {
            success: true,
            status: 200,
            data: {a: 42}
        };

        // Act
        const result = ResponseValidator.extractData(json, 'GET /api/x');

        // Assert
        expect(result.a).toBe(42);
    });

    it('throws error when success is false', () => {
        // Arrange
        const json = {
            success: false,
            code: 'ERR',
            status: 500,
            data: null
        } as UseCaseResponse<null>;

        // Act
        const act = () => ResponseValidator.extractData(json, 'GET /api/x');

        // Assert
        expect(act).toThrowError('Malformed response for GET /api/x');
    });

    it('throws error when data is null', () => {
        // Arrange
        const json = {
            success: true,
            code: 'OK',
            status: 200,
            data: null
        } as UseCaseResponse<null>;

        // Act
        const act = () => ResponseValidator.extractData(json, 'GET /api/x');

        // Assert
        expect(act).toThrowError('Malformed response for GET /api/x');
    });
});
