// frontend/tests/helpers/http/responses/entries/AddEntryResponseFactory.ts
// Purpose: build consistent mocked API responses for UC-1 AddEntry gateway tests.
// No imports from src — only plain JavaScript objects.

import {EntryFactory} from '@tests/helpers/factories/EntryFactory';

/**
 * AC-01 — Happy Path response.
 *
 * Builds a mocked API JSON object corresponding to a successful AddEntry response:
 *  {
 *      success: true,
 *      status: 200,
 *      data: { item: { ...entry } }
 *  }
 *
 * The provided request must contain title, body, and date.
 * EntryFactory is used to generate the domain-like object for data.item.
 *
 * @param {{title: string, body: string, date: string}} request
 * @returns {{success: true, status: 200, data: {item: object}}} Mocked API payload.
 */
export function ac01HappyPath(request: {title: string; body: string; date: string}) {
    const title = `${request.title} (UC-01, AC-01)`;

    const item = EntryFactory.make({
        title,
        body: request.body,
        date: request.date
    });

    // prettier-ignore
    const payload = {
        success: true,
        status : 200,
        data   : {item}
    };

    return payload;
}
