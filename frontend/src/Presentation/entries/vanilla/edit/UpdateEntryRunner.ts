import type {UseCaseResponse} from '@src/Application/DTO/Common/UseCaseResponse';
import type {Entry} from '@src/Domain/Models/Entries/Entry';
import {UseCaseRunner} from '@src/Presentation/runner/UseCaseRunner';
import {makeUpdateEntryUseCase} from '@src/Configuration/Providers/Entries/UpdateEntryProvider';

export interface UpdateEntryRunner {
    // eslint-disable-next-line no-unused-vars
    run: (params: {
        id: string;
        title: string;
        body: string;
        date: string;
    }) => Promise<UseCaseResponse<Entry>>;
}

export function createUpdateEntryRunner(): UpdateEntryRunner {
    const useCase = makeUpdateEntryUseCase();

    const runner: UpdateEntryRunner = {
        run: async (params: {
            id: string;
            title: string;
            body: string;
            date: string;
        }): Promise<UseCaseResponse<Entry>> => {
            const runnerResult = await UseCaseRunner.run(useCase, params);
            const response = runnerResult as UseCaseResponse<Entry>;

            return response;
        }
    };

    return runner;
}
