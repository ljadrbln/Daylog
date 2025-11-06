// frontend/tests/Unit/Presentation/UseCaseRunner/AC01_HappyPath.test.ts
import {describe, it, expect, vi} from 'vitest';
import {UseCaseRunner} from '@src/Presentation/runner/UseCaseRunner';

/**
 * AC-01 — Happy Path → normalized success envelope.
 *
 * Purpose:
 * Verify that UseCaseRunner.run() acts as a Presentation-level wrapper
 * for any Application use case. When the use case resolves successfully,
 * the runner must normalize its output to a UI-friendly envelope:
 * { success:true, status:200, data }.
 *
 * Mechanics:
 * - Arrange:
 *   - Create fake DTO types and fake data.
 *   - Build fake use case (object with execute(req)) that resolves
 *     to an object containing only { data } (no status/success).
 *   - Spy on fakeExecute to confirm it was called once with the same request.
 * - Act:
 *   - Call UseCaseRunner.run(fakeUseCase, fakeRequest).
 * - Assert:
 *   - success === true
 *   - status === 200
 *   - data is the same reference as fakeData (no cloning)
 *   - execute called exactly once with fakeRequest.
 *
 * Cases:
 * - AC01: Happy path only. Error mapping is handled in AC02–AC04.
 *
 * @covers UseCaseRunner.run
 */
describe('UseCaseRunner — AC01_HappyPath', () => {
    it('returns {success:true, status:200, data} and invokes execute(req) once', async () => {
        // Arrange
        type FakeRequestDto = {any: string};
        type FakeDataDto = {value: number};

        const fakeRequest: FakeRequestDto = {any: 'input'};
        const fakeData: FakeDataDto = {value: 42};

        const fakeExecute = vi.fn(async (req: FakeRequestDto) => {
            void req;
            const payload = {data: fakeData}; // no success/status; runner adds them

            return payload;
        });

        const fakeUseCase = {
            /**
             * Fake Application use case used only to simulate successful resolution.
             */
            execute: fakeExecute
        };

        // Act
        const envelope = await UseCaseRunner.run<FakeRequestDto, FakeDataDto>(
            fakeUseCase,
            fakeRequest
        );

        // Assert (atomic, no chained calls)
        const callsCount = fakeExecute.mock.calls.length;
        expect(callsCount).toBe(1);

        const firstCallArgs = fakeExecute.mock.calls[0];
        const calledWith = firstCallArgs[0];
        expect(calledWith).toBe(fakeRequest);

        const isSuccess = envelope.success;
        expect(isSuccess).toBe(true);

        const status = envelope.status;
        expect(status).toBe(200);

        const data = envelope.data;
        expect(data).toBe(fakeData);
    });
});
