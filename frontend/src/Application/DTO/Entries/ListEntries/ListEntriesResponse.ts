import type {Entry} from '../../../../Domain/Entries/Entry';
import type {UseCaseResponse} from '../../Common/UseCaseResponse';

export interface ListEntriesData {
    items: Entry[];
    page: number;
    perPage: number;
    total: number;
    pagesCount: number;
}

export interface ListEntriesResponse extends UseCaseResponse<ListEntriesData> {}
