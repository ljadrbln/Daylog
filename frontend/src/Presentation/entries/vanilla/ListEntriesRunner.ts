import type {UseCaseResponse} from '@src/Application/DTO/Common/UseCaseResponse';
import type {ListEntriesPageInterface} from '@src/Domain/Interfaces/Entries/ListEntriesPageInterface';
import {UseCaseRunner} from '@src/Presentation/runner/UseCaseRunner';
import {makeListEntriesUseCase} from '@src/Configuration/Providers/Entries/ListEntriesProvider';

export interface ListEntriesRunner {
    run: () => Promise<UseCaseResponse<ListEntriesPageInterface>>;
}

export function createListEntriesRunner(): ListEntriesRunner {
    const useCase = makeListEntriesUseCase();

    const listEntriesRunner: ListEntriesRunner = {
        run: async (): Promise<UseCaseResponse<ListEntriesPageInterface>> => {
            const params = {};

            const runnerResult = await UseCaseRunner.run(useCase, params);
            const response = runnerResult as UseCaseResponse<ListEntriesPageInterface>;

            return response;
        }
    };

    return listEntriesRunner;
}
