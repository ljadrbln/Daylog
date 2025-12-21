/**
 * Entry file for vanilla implementation of UC-2 (ListEntries).
 *
 * Purpose:
 * - Register <dl-list-entries> custom element in the global registry.
 * - Keep tag name stable so different JS bundles (vanilla/lit/etc.)
 *   can swap implementations without changing HTML.
 */

import {ListEntriesElement} from '@src/Presentation/entries/vanilla/list/ListEntriesElement';

/**
 * Stable custom element tag name for UC-2 ListEntries.
 */
const tagName = 'dl-list-entries';

/**
 * Register ListEntriesElement under the stable tag name if not
 * registered yet. This makes the entry file idempotent and safe
 * to import from multiple places.
 *
 * @returns {void}
 */
function registerListEntriesElement(): void {
    const existing = customElements.get(tagName);

    if (!existing) {
        const ctor = ListEntriesElement;

        customElements.define(tagName, ctor);
    }
}

registerListEntriesElement();
