import type { Entry } from '@src/Domain/Entries/Entry';

export type ListEntriesData = {
    items: Entry[];
    page: number;
    perPage: number;
    total: number;
    pagesCount: number;
};
