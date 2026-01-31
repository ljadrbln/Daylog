/**
 * DeleteEntryRunner provides a thin adapter between the Entry view UI
 * and UC-4 (DeleteEntry) use case.
 *
 * Purpose:
 * - Execute UC-4 via UseCaseRunner.
 * - Keep deletion logic isolated from the UI component.
 *
 * Notes:
 * - No HTTP calls here.
 * - No DOM access.
 * - Mirrors other runners (GetEntry, ListEntries, AddEntry).
 */

import type {UseCaseResponse} from '@src/Application/DTO/Common/UseCaseResponse';
import {UseCaseRunner} from '@src/Presentation/runner/UseCaseRunner';
import {makeDeleteEntryUseCase} from '@src/Configuration/Providers/Entries/DeleteEntryProvider';

export interface DeleteEntryRunner {
    // eslint-disable-next-line no-unused-vars
    run: (entryId: string) => Promise<UseCaseResponse<null>>;
}

/**
 * Factory for DeleteEntryRunner.
 *
 * @returns {DeleteEntryRunner}
 */
export function createDeleteEntryRunner(): DeleteEntryRunner {
    const useCase = makeDeleteEntryUseCase();

    const runner: DeleteEntryRunner = {
        run: async (entryId: string): Promise<UseCaseResponse<null>> => {
            const params = {
                id: entryId
            };

            const runnerResult = await UseCaseRunner.run(useCase, params);
            const response = runnerResult as UseCaseResponse<null>;

            return response;
        }
    };

    return runner;
}
