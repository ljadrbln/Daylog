/**
 * @file edit.vanilla.entry.test.ts
 *
 * This test verifies that the vanilla entry file for UC-5 (EditEntry)
 * registers the <dl-entry-edit> custom element in the CustomElementRegistry.
 *
 * It guarantees:
 * - the HTML tag remains a stable contract;
 * - the registered constructor is EditEntryElement;
 * - switching bundles later (vanilla/lit/etc.) only changes the implementation
 *   behind the same tag name.
 */

import {describe, it, expect} from 'vitest';
import {EditEntryElement} from '@src/Presentation/entries/vanilla/edit/EditEntryElement';

describe('edit.vanilla.entry', () => {
    it('registers <dl-entry-edit> with EditEntryElement constructor', async () => {
        const tagName = 'dl-entry-edit';

        // Import entry file that performs registration as a side effect.
        await import('@src/Presentation/entries/vanilla/edit/edit.vanilla.entry');

        const registeredConstructor = customElements.get(tagName);

        expect(registeredConstructor).toBe(EditEntryElement);
    });
});
