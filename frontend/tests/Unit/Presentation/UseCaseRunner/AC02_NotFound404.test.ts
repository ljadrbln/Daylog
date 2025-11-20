// frontend/tests/Unit/Presentation/UseCaseRunner/AC02_NotFound404.test.ts
import {describe, it, expect, vi} from 'vitest';
import {UseCaseRunner} from '@src/Presentation/runner/UseCaseRunner';

/**
 * AC-02 — NotFound (404) → normalized error envelope.
 *
 * Purpose:
 * Verify that UseCaseRunner.run() maps a rejected use case with status 404
 * into a UI-friendly error envelope with code ENTRY_NOT_FOUND.
 *
 * Mechanics:
 * - Arrange:
 *   - Define fake request DTO.
 *   - Build fake use case whose execute(req) throws an error object with { status:404 }.
 *   - Spy on execute to confirm it was called once with the same request.
 * - Act:
 *   - Call UseCaseRunner.run(fakeUseCase, fakeRequest).
 * - Assert:
 *   - success === false
 *   - status === 404
 *   - code === 'ENTRY_NOT_FOUND'
 *   - execute called exactly once with fakeRequest.
 *
 * Cases:
 * - AC02: First error scenario — 404 Not Found mapped to ENTRY_NOT_FOUND.
 *
 * @covers UseCaseRunner.run
 */
describe('UseCaseRunner — AC02_NotFound404', () => {
    it('maps status 404 to {success:false, status:404, code:"ENTRY_NOT_FOUND"}', async () => {
        // Arrange
        type FakeRequestDto = {id: string};
        type FakeDataDto = unknown;

        const fakeRequest: FakeRequestDto = {
            id: '00000000-0000-0000-0000-000000000000'
        };

        const fakeExecute = vi.fn(async (req: FakeRequestDto) => {
            void req;

            const error = {
                status: 404
            };

            throw error;
        });

        const fakeUseCase = {
            /**
             * Fake Application use case used only to simulate 404 rejection.
             */
            execute: fakeExecute
        };

        // Act
        const envelope = await UseCaseRunner.run<FakeRequestDto, FakeDataDto>(
            fakeUseCase,
            fakeRequest
        );

        // Assert (atomic, no derived expressions inside expect)
        const callsCount = fakeExecute.mock.calls.length;
        expect(callsCount).toBe(1);

        const firstCallArgs = fakeExecute.mock.calls[0];
        const calledWith = firstCallArgs[0];
        expect(calledWith).toBe(fakeRequest);

        const isSuccess = envelope.success;
        expect(isSuccess).toBe(false);

        const status = envelope.status;
        expect(status).toBe(404);

        const code = envelope.code;
        expect(code).toBe('ENTRY_NOT_FOUND');
    });
});
