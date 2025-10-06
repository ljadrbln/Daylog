import type {Entry} from '@src/Domain/Entries/Entry';

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
        const entry: Entry = {
            id: '23d90e4f-e736-4260-8c31-ae9124fb9280',
            title: 'Valid title',
            body: 'Valid body',
            date: '2025-02-12',
            createdAt: '2025-10-04T15:01:18+00:00',
            updatedAt: '2025-10-04T15:01:18+00:00'
        };

        return entry;
    }

    /**
     * AC-04 — Success False
     * Returns a valid entry for success=false scenario
     *
     * @returns {Entry} Valid entry.
     */
    static ac04SuccessFalse(): Entry {
        const entry = this.ac01HappyPath();
        return entry;
    }
}
