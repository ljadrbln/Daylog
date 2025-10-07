import type {Entry} from '@src/Domain/Entries/Entry';
import {EntryFactory} from '@tests/helpers/factories/EntryFactory';

/**
 * UC-1 (Add Entry) — response item datasets.
 *
 * Purpose:
 * Provide deterministic Entry objects (what backend returns in data.item).
 */
export class AddEntryDataset {
    /**
     * AC-01 — Happy Path item.
     */
    static ac01HappyPath(): Entry {
        const entry = EntryFactory.make({title: 'Valid title (UC-01, AC-01)'});
        return entry;
    }

    /**
     * AC-04 — Success=false.
     */
    static ac04SuccessFalse(): Entry {
        const entry = EntryFactory.make({title: 'Valid title (UC-01, AC-04)'});
        return entry;
    }
}
