// frontend/tests/Unit/Presentation/UseCaseRunner/AC04_Unexpected500.test.ts
import {describe, it, expect, vi} from 'vitest';
import {UseCaseRunner} from '@src/Presentation/runner/UseCaseRunner';

/**
 * AC-04 — Unexpected (500) → normalized error envelope.
 *
 * Purpose:
 * Verify that UseCaseRunner.run() maps unexpected or unclassified errors
 * (status >= 500 or missing code) into a generic UNKNOWN_ERROR envelope.
 *
 * Mechanics:
 * - Arrange:
 *   - Fake request DTO.
 *   - Fake use case whose execute(req) throws { status:500 } without a code.
 *   - Spy to confirm execute was called once.
 * - Act:
 *   - Call UseCaseRunner.run(fakeUseCase, fakeRequest).
 * - Assert:
 *   - success === false
 *   - status === 500
 *   - code === 'UNKNOWN_ERROR'
 *
 * Cases:
 * - AC04: Unexpected error (fallback branch).
 *
 * @covers UseCaseRunner.run
 */
describe('UseCaseRunner — AC04_Unexpected500', () => {
    it('maps status 500 to UNKNOWN_ERROR', async () => {
        // Arrange
        type FakeRequestDto = {op: string};
        type FakeDataDto = unknown;

        const fakeRequest: FakeRequestDto = {op: 'test'};

        const fakeExecute = vi.fn(async (req: FakeRequestDto) => {
            void req;

            const error = {
                status: 500
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
        expect(status).toBe(500);

        const code = envelope.code;
        expect(code).toBe('UNKNOWN_ERROR');
    });
});
