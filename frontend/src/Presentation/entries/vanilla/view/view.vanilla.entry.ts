/**
 * Entry file for vanilla implementation of UC-3 (GetEntry).
 *
 * Purpose:
 * - Register <dl-entry> custom element in the global registry.
 * - Keep tag name stable so different JS bundles (vanilla/lit/etc.)
 *   can swap implementations without changing HTML.
 */

import {EntryElement} from '@src/Presentation/entries/vanilla/view/EntryElement';

/**
 * Stable custom element tag name for UC-3 GetEntry.
 */
const tagName = 'dl-entry';

/**
 * Register EntryElement under the stable tag name if not
 * registered yet. This makes the entry file idempotent and safe
 * to import from multiple places.
 *
 * @returns {void}
 */
function registerEntryElement(): void {
    const existing = customElements.get(tagName);

    if (!existing) {
        const ctor = EntryElement;

        customElements.define(tagName, ctor);
    }
}

registerEntryElement();
