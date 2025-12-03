/**
 * @file list.vanilla.entry.test.ts
 *
 * This test verifies that the vanilla entry file for UC-2 (ListEntries)
 * registers the <dl-list-entries> custom element in the CustomElementRegistry.
 *
 * It guarantees:
 * - the HTML tag remains a stable contract;
 * - the registered constructor is ListEntriesElement;
 * - switching bundles later (vanilla/lit/etc.) only changes the implementation
 *   behind the same tag name.
 */

import {describe, it, expect} from 'vitest';
import {ListEntriesElement} from '@src/Presentation/entries/vanilla/ListEntriesElement';

describe('list.vanilla.entry', () => {
    it('registers <dl-list-entries> with ListEntriesElement constructor', async () => {
        const tagName = 'dl-list-entries';

        // Import entry file that performs registration as a side effect.
        await import('@src/Presentation/entries/vanilla/list.vanilla.entry');

        const registeredConstructor = customElements.get(tagName);

        expect(registeredConstructor).toBe(ListEntriesElement);
    });
});
