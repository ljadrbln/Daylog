import type {AddEntryResponse} from '@src/Application/DTO/Entries/AddEntry/AddEntryResponse';
import {UseCaseRunner} from '@src/Presentation/runner/UseCaseRunner';
import {makeAddEntryUseCase} from '@src/Configuration/Providers/Entries/AddEntryProvider';

export interface AddEntryRunner {
    // eslint-disable-next-line no-unused-vars
    run: (params: {title: string; body: string; date: string}) => Promise<AddEntryResponse>;
}

export function createAddEntryRunner(): AddEntryRunner {
    const useCase = makeAddEntryUseCase();

    const runner: AddEntryRunner = {
        run: async (params: {
            title: string;
            body: string;
            date: string;
        }): Promise<AddEntryResponse> => {
            const runnerResult = await UseCaseRunner.run(useCase, params);
            const response = runnerResult as AddEntryResponse;

            return response;
        }
    };

    return runner;
}
