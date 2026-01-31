/**
 * Entry file for vanilla implementation of UC-1 (AddEntry).
 *
 * Purpose:
 * - Register <dl-entry-add> custom element in the global registry.
 * - Keep tag name stable so different JS bundles (vanilla/lit/etc.)
 *   can swap implementations without changing HTML.
 */

import {AddEntryElement} from '@src/Presentation/entries/vanilla/add/AddEntryElement';

/**
 * Stable custom element tag name for UC-1 AddEntry.
 */
const tagName = 'dl-entry-add';

/**
 * Register AddEntryElement under the stable tag name if not
 * registered yet. This makes the entry file idempotent and safe
 * to import from multiple places.
 *
 * @returns {void}
 */
function registerAddEntryElement(): void {
    const existing = customElements.get(tagName);

    if (!existing) {
        const ctor = AddEntryElement;

        customElements.define(tagName, ctor);
    }
}

registerAddEntryElement();
