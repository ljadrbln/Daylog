import type {Entry} from '@src/Domain/Entries/Entry';
import type {UseCaseResponse} from '@src/Application/DTO/Common/UseCaseResponse';

export interface ListEntriesData {
    items: Entry[];
    page: number;
    perPage: number;
    total: number;
    pagesCount: number;
}

export interface ListEntriesResponse extends UseCaseResponse<ListEntriesData> {}
