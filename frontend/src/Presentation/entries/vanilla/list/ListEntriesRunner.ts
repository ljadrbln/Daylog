import type {UseCaseResponse} from '@src/Application/DTO/Common/UseCaseResponse';
import type {ListEntriesData} from '@src/Application/DTO/Entries/ListEntries/ListEntriesResponse';
import {UseCaseRunner} from '@src/Presentation/runner/UseCaseRunner';
import {makeListEntriesUseCase} from '@src/Configuration/Providers/Entries/ListEntriesProvider';

export interface ListEntriesRunner {
    run: () => Promise<UseCaseResponse<ListEntriesData>>;
}

export function createListEntriesRunner(): ListEntriesRunner {
    const useCase = makeListEntriesUseCase();

    const listEntriesRunner: ListEntriesRunner = {
        run: async (): Promise<UseCaseResponse<ListEntriesData>> => {
            const params = {};

            const runnerResult = await UseCaseRunner.run(useCase, params);
            const response = runnerResult as UseCaseResponse<ListEntriesData>;

            return response;
        }
    };

    return listEntriesRunner;
}
