import type {Entry} from '@src/Domain/Entries/Entry';
import {EntryFactory} from '@tests/helpers/factories/EntryFactory';

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
        const first = EntryFactory.make({
            id: '591aef97-d0ae-4d86-b2cc-2a2ef6a71918',
            title: 'Valid title (AC-01 #1)',
            date: '2025-02-14'
        });

        const second = EntryFactory.make({
            id: 'a68b6643-e713-4266-a7d5-8fad9d6f402a',
            title: 'Valid title (AC-01 #2)',
            date: '2025-02-12'
        });

        const items: Entry[] = [first, second];
        return items;
    }
}
