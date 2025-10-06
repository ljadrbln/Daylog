import type {Entry} from '@src/Domain/Entries/Entry';
import {EntryFactory} from '@tests/helpers/factories/EntryFactory';

/**
 * UC-3 datasets for frontend gateway tests.
 *
 * Purpose:
 * Provide a single, shared source of truth for sample Entry objects
 * used across acceptance-criteria tests (AC-01…AC-04).
 *
 * Mechanics:
 * - Each method returns domain-shaped data that satisfies BR-2 timestamp
 *   invariants and ENTRY-BR-4 (logical date).
 */
export class GetEntryDataset {
    /**
     * AC-01 — Happy Path.
     * Returns a valid Entry object with stable, deterministic values.
     *
     * @returns {Entry} Valid entry.
     */
    static ac01HappyPath(): Entry {
        const entry = EntryFactory.make({title: 'Valid title (UC-03, AC-01)'});

        return entry;
    }

    /**
     * AC-04 — Success False
     * Returns a valid entry for success=false scenario
     *
     * @returns {Entry} Valid entry.
     */
    static ac04SuccessFalse(): Entry {
        const entry = EntryFactory.make({title: 'Valid title (UC-03, AC-04)'});

        return entry;
    }
}
