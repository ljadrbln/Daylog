// tests/helpers/http/responses/entries/UpdateEntryResponseFactory.ts
import {EntryFactory} from '@tests/helpers/factories/EntryFactory';
import type {Entry} from '@src/Domain/Entries/Entry';
import type {UseCaseResponse} from '@src/Application/DTO/Common/UseCaseResponse';
import type {UpdateEntryRequest} from '@src/Application/DTO/Entries/UpdateEntry/UpdateEntryRequest';

/**
 * Provides consistent mocked API responses for UC-5 UpdateEntry gateway tests.
 *
 * Happy path:
 * - { success: true, status: 200, data: Entry }
 * Data mirrors "updated" state derived from request fields.
 */
export function ac01HappyPath(request: UpdateEntryRequest): UseCaseResponse<Entry> {
    // prettier-ignore
    const item = EntryFactory.make({
        id   : request.id,
        title: request.title,
        body : request.body,
        date : request.date
    });

    // prettier-ignore
    const response: UseCaseResponse<Entry> = {
        success: true,
        status : 200,
        data   : item
    };

    return response;
}

/**
 * AC-03 — Malformed JSON (success=true but missing `data`).
 */
export interface MalformedUpdateSuccessPayload {
    success: true;
    status: number;
    // intentionally no `data`
}

/**
 * @returns {MalformedUpdateSuccessPayload} success=true payload without `data`.
 */
export function ac03MalformedJson(_request: UpdateEntryRequest): MalformedUpdateSuccessPayload {
    void _request;

    // prettier-ignore
    const payload: MalformedUpdateSuccessPayload = {
        success: true,
        status : 200
        // no `data`
    };

    return payload;
}

/**
 * AC-04 — success=false with HTTP 200.
 */
export function ac04SuccessFalse(_request: UpdateEntryRequest): UseCaseResponse<Entry> {
    void _request;

    // prettier-ignore
    const payload: UseCaseResponse<Entry> = {
        success: false,
        status : 200,
        // no data on error branch per our envelope
    };

    return payload;
}
