import {uuidv4} from '@tests/helpers/utils/uuid';
import type {Entry} from '@src/Domain/Entries/Entry';

/**
 * Domain Entry factory for repository tests.
 *
 * Provides consistent, valid Entry objects for both create() and update() flows.
 * Uses overrides to customize fields instead of duplicating structures.
 */
export class EntryFactory {
    /**
     * Build a generic valid Entry object.
     *
     * Mechanics:
     * - Generates UUID v4 id.
     * - Assigns fixed timestamps.
     * - Applies overrides last (to allow id='', empty timestamps, etc.).
     *
     * @param {Partial<Entry>} overrides Optional field overrides.
     * @returns {Entry} Ready-to-use Entry object.
     */
    public static make(overrides: Partial<Entry> = {}): Entry {
        const now = new Date().toISOString();

        const base: Entry = {
            id: uuidv4(),
            title: 'Valid title',
            body: 'Valid body',
            date: '2025-02-12',
            createdAt: now,
            updatedAt: now
        };

        const entry: Entry = {...base, ...overrides};
        return entry;
    }

    /**
     * Build Entry for create() branch.
     *
     * Mechanics:
     * - Overrides id='', createdAt='', updatedAt=''.
     * - Used to trigger POST /api/entries.
     *
     * @param {Partial<Entry>} overrides Optional field overrides.
     * @returns {Entry} Entry suitable for creation flow.
     */
    public static makeForCreate(overrides: Partial<Entry> = {}): Entry {
        const entry = this.make({
            id: '',
            createdAt: '',
            updatedAt: '',
            ...overrides
        });

        return entry;
    }

    /**
     * Build Entry for update() branch.
     *
     * Mechanics:
     * - Leaves id and timestamps populated.
     * - Used to trigger PUT /api/entries/{id}.
     *
     * @param {Partial<Entry>} overrides Optional field overrides.
     * @returns {Entry} Entry suitable for update flow.
     */
    public static makeForUpdate(overrides: Partial<Entry> = {}): Entry {
        const entry = this.make(overrides);

        return entry;
    }
}
