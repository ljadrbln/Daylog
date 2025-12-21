import type {UseCaseResponse} from '@src/Application/DTO/Common/UseCaseResponse';
import type {GetEntryResponse} from '@src/Application/DTO/Entries/GetEntry/GetEntryResponse';
import {UseCaseRunner} from '@src/Presentation/runner/UseCaseRunner';
import {makeGetEntryUseCase} from '@src/Configuration/Providers/Entries/GetEntryProvider';

export interface EntryRunner {
    // eslint-disable-next-line no-unused-vars
    run: (entryId: string) => Promise<UseCaseResponse<GetEntryResponse>>;
}

export function createEntryRunner(): EntryRunner {
    const useCase = makeGetEntryUseCase();

    const entryRunner: EntryRunner = {
        run: async (entryId: string): Promise<UseCaseResponse<GetEntryResponse>> => {
            const params = {
                id: entryId
            };

            const runnerResult = await UseCaseRunner.run(useCase, params);
            const response = runnerResult as UseCaseResponse<GetEntryResponse>;

            return response;
        }
    };

    return entryRunner;
}
