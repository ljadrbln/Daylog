/**
 * @file add.vanilla.entry.test.ts
 *
 * This test verifies that the vanilla entry file for UC-1 (AddEntry)
 * registers the <dl-entry-add> custom element in the CustomElementRegistry.
 *
 * It guarantees:
 * - the HTML tag remains a stable contract;
 * - the registered constructor is AddEntryElement;
 * - switching bundles later (vanilla/lit/etc.) only changes the implementation
 *   behind the same tag name.
 */

import {describe, it, expect} from 'vitest';
import {AddEntryElement} from '@src/Presentation/entries/vanilla/add/AddEntryElement';

describe('add.vanilla.entry', () => {
    it('registers <dl-entry-add> with AddEntryElement constructor', async () => {
        const tagName = 'dl-entry-add';

        // Import entry file that performs registration as a side effect.
        await import('@src/Presentation/entries/vanilla/add/add.vanilla.entry');

        const registeredConstructor = customElements.get(tagName);

        expect(registeredConstructor).toBe(AddEntryElement);
    });
});
