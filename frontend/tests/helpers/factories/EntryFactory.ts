// tests/helpers/factories/EntryFactory.ts
import type {Entry} from '@src/Domain/Entries/Entry';
import {uuidv4} from '@tests/helpers/utils/uuid';

/**
 * Factory for building deterministic Entry objects used in tests.
 *
 * Purpose:
 * Provide a single, reusable generator for Entry domain models
 * across all datasets (GetEntry, ListEntries, UpdateEntry, etc.).
 *
 * Mechanics:
 * - Produces a valid baseline Entry that satisfies domain rules.
 * - Accepts partial overrides to change specific fields.
 */
export class EntryFactory {
    /**
     * Builds a valid Entry object with optional overrides.
     *
     * @param {Partial<Entry>} overrides Optional field overrides.
     * @returns {Entry} Valid Entry object.
     */
    static make(overrides: Partial<Entry> = {}): Entry {
        const base: Entry = {
            id: uuidv4(),
            title: 'Valid title',
            body: 'Valid body',
            date: '2025-02-12',
            createdAt: '2025-10-04T15:01:18+00:00',
            updatedAt: '2025-10-04T15:01:18+00:00'
        };

        const entry: Entry = {...base, ...overrides};
        return entry;
    }
}
