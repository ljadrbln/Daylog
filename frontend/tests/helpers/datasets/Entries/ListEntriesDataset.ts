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
            title: 'First entry',
            date: '2025-02-14'
        });

        const second = EntryFactory.make({
            title: 'Second entry',
            date: '2025-02-12'
        });

        const items: Entry[] = [first, second];
        return items;
    }
}
