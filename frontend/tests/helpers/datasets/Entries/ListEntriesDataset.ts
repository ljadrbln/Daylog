// tests/helpers/datasets/ListEntriesDataset.ts
import type {Entry} from '@src/Domain/Entries/Entry';

/**
 * UC-2 datasets for frontend gateway tests.
 *
 * Purpose:
 * Provide shared arrays of Entry objects for list scenarios, keeping
 * AC-01…AC-04 tests consistent and DRY.
 *
 * Notes:
 * - Default sort in UC-2 is "date DESC". The returned array follows
 *   this order so tests can assert deterministic behavior.
 */
export class ListEntriesDataset {
    /**
     * AC-01 — Happy Path (default page, no filters).
     * Returns entries sorted by date DESC.
     *
     * @returns {Entry[]} Array of valid entries.
     */
    static ac01HappyPath(): Entry[] {
        const first: Entry = {
            id: '591aef97-d0ae-4d86-b2cc-2a2ef6a71918',
            title: 'Valid title',
            body: 'Valid body',
            date: '2025-02-14',
            createdAt: '2025-02-12T10:00:02+00:00',
            updatedAt: '2025-02-12T10:00:02+00:00'
        };

        const second: Entry = {
            id: 'a68b6643-e713-4266-a7d5-8fad9d6f402a',
            title: 'Valid title',
            body: 'Valid body',
            date: '2025-02-12',
            createdAt: '2025-02-11T09:00:02+00:00',
            updatedAt: '2025-02-11T09:00:02+00:00'
        };

        const items: Entry[] = [first, second];
        return items;
    }
}
