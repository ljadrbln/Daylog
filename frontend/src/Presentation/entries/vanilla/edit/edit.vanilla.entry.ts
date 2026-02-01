/**
 * Entry file for vanilla implementation of UC-5 (EditEntry).
 *
 * Purpose:
 * - Register <dl-entry-edit> custom element in the global registry.
 * - Keep tag name stable so different JS bundles (vanilla/lit/etc.)
 *   can swap implementations without changing HTML.
 */

import {EditEntryElement} from '@src/Presentation/entries/vanilla/edit/EditEntryElement';

/**
 * Stable custom element tag name for UC-5 EditEntry.
 */
const tagName = 'dl-entry-edit';

/**
 * Register EditEntryElement under the stable tag name if not
 * registered yet. This makes the entry file idempotent and safe
 * to import from multiple places.
 *
 * @returns {void}
 */
function registerEditEntryElement(): void {
    const existing = customElements.get(tagName);

    if (!existing) {
        const ctor = EditEntryElement;

        customElements.define(tagName, ctor);
    }
}

registerEditEntryElement();
