// frontend/tests/Unit/Presentation/UseCaseRunner/AC03_Validation422.test.ts
import {describe, it, expect, vi} from 'vitest';
import {UseCaseRunner} from '@src/Presentation/runner/UseCaseRunner';

/**
 * AC-03 — Validation (422) → normalized error envelope.
 *
 * Purpose:
 * Verify that UseCaseRunner.run() maps a rejected use case with status 422
 * into a UI-friendly error envelope with code VALIDATION_ERROR and
 * preserves the errors object.
 *
 * Mechanics:
 * - Arrange:
 *   - Fake request DTO.
 *   - Fake use case whose execute(req) throws { status:422, errors:{...} }.
 *   - Spy to confirm execute was called once with the same request.
 * - Act:
 *   - Call UseCaseRunner.run(fakeUseCase, fakeRequest).
 * - Assert:
 *   - success === false
 *   - status === 422
 *   - code === 'VALIDATION_ERROR'
 *   - errors are preserved
 *   - execute called exactly once
 *
 * Cases:
 * - AC03: Validation error mapping.
 *
 * @covers UseCaseRunner.run
 */
describe('UseCaseRunner — AC03_Validation422', () => {
    it('maps status 422 to VALIDATION_ERROR and preserves errors', async () => {
        // Arrange
        type FakeRequestDto = {title: string};
        type FakeDataDto = unknown;

        const fakeRequest: FakeRequestDto = {title: ''};

        const validationErrors = {
            TITLE_REQUIRED: true,
            BODY_TOO_LONG: false
        };

        const fakeExecute = vi.fn(async (req: FakeRequestDto) => {
            void req;

            const error = {
                status: 422,
                errors: validationErrors
            };

            throw error;
        });

        const fakeUseCase = {
            execute: fakeExecute
        };

        // Act
        const envelope = await UseCaseRunner.run<FakeRequestDto, FakeDataDto>(
            fakeUseCase,
            fakeRequest
        );

        // Assert (atomic)
        const callsCount = fakeExecute.mock.calls.length;
        expect(callsCount).toBe(1);

        const firstCallArgs = fakeExecute.mock.calls[0];
        const calledWith = firstCallArgs[0];
        expect(calledWith).toBe(fakeRequest);

        const isSuccess = envelope.success;
        expect(isSuccess).toBe(false);

        const status = envelope.status;
        expect(status).toBe(422);

        const code = envelope.code;
        expect(code).toBe('VALIDATION_ERROR');

        const errors = envelope.errors;
        expect(errors).toBe(validationErrors);
    });
});
