/**
 * @file EntryElement.test.ts
 *
 * This test suite describes the behavior of the vanilla Web Component
 * <dl-entry> for UC-3 (GetEntry) with the delete action wired to UC-4 (DeleteEntry).
 *
 * Scenarios:
 * - shows loading state right after being connected to the DOM;
 * - renders entry data when UC-3 returns a successful response;
 * - renders error state when UC-3 returns a failed response;
 * - triggers UC-4 delete flow when user confirms deletion;
 * - renders error message when UC-4 returns a failed response;
 * - renders generic delete error message when UC-4 runner throws.
 */

import {describe, it, expect, vi, beforeAll, beforeEach} from 'vitest';
import {EntryElement} from '@src/Presentation/entries/vanilla/view/EntryElement';
import type {UseCaseResponse} from '@src/Application/DTO/Common/UseCaseResponse';
import type {GetEntryResponse} from '@src/Application/DTO/Entries/GetEntry/GetEntryResponse';
import type {EntryRunner} from '@src/Presentation/entries/vanilla/view/EntryRunner';
import type {DeleteEntryRunner} from '@src/Presentation/entries/vanilla/view/DeleteEntryRunner';

/**
 * Ensures that EntryElement constructor is registered
 * in the CustomElementRegistry before we instantiate it directly.
 *
 * jsdom requires the constructor to be part of the registry;
 * otherwise "Invalid constructor" is thrown.
 *
 * @returns {void}
 */
function ensureCustomElementRegistered(): void {
    const tagName = 'dl-entry';
    const existing = customElements.get(tagName);

    if (!existing) {
        const ctor = EntryElement;
        customElements.define(tagName, ctor);
    }
}

/**
 * Wraps entry into a successful use-case response.
 *
 * @param {GetEntryResponse} entry Entry data returned by UC-3.
 *
 * @returns {UseCaseResponse<GetEntryResponse>} Successful response with provided data.
 */
function createGetSuccessResponse(entry: GetEntryResponse): UseCaseResponse<GetEntryResponse> {
    const response: UseCaseResponse<GetEntryResponse> = {
        success: true,
        code: null,
        status: 200,
        data: entry,
        errors: null
    };

    return response;
}

/**
 * Wraps error information into a failed use-case response for UC-3.
 *
 * @param {string} code Machine-readable error code.
 * @param {string[]} errors Human-readable error messages.
 *
 * @returns {UseCaseResponse<GetEntryResponse>} Failed response without data.
 */
function createGetErrorResponse(code: string, errors: string[]): UseCaseResponse<GetEntryResponse> {
    const response: UseCaseResponse<GetEntryResponse> = {
        success: false,
        code,
        status: 404,
        data: null,
        errors
    };

    return response;
}

/**
 * Wraps UC-4 delete result into a successful response.
 *
 * @returns {UseCaseResponse<null>} Successful delete response.
 */
function createDeleteSuccessResponse(): UseCaseResponse<null> {
    const response: UseCaseResponse<null> = {
        success: true,
        code: null,
        status: 200,
        data: null,
        errors: null
    };

    return response;
}

/**
 * Wraps UC-4 delete result into a failed response.
 *
 * @param {string} code Machine-readable error code.
 * @param {string[]} errors Human-readable error messages.
 *
 * @returns {UseCaseResponse<null>} Failed delete response.
 */
function createDeleteErrorResponse(code: string, errors: string[]): UseCaseResponse<null> {
    const response: UseCaseResponse<null> = {
        success: false,
        code,
        status: 500,
        data: null,
        errors
    };

    return response;
}

/**
 * Creates a fake UC-3 runner resolving with the provided response.
 *
 * @param {UseCaseResponse<GetEntryResponse>} response Predefined UC-3 response.
 *
 * @returns {EntryRunner} Runner whose run() resolves with response.
 */
function createFakeGetRunner(response: UseCaseResponse<GetEntryResponse>): EntryRunner {
    const spy = vi.fn<[string], Promise<UseCaseResponse<GetEntryResponse>>>();
    spy.mockResolvedValue(response);

    const runner: EntryRunner = {
        run: spy
    };

    return runner;
}

/**
 * Creates a fake UC-4 runner resolving with the provided response.
 *
 * @param {UseCaseResponse<null>} response Predefined UC-4 response.
 *
 * @returns {DeleteEntryRunner} Runner whose run() resolves with response.
 */
function createFakeDeleteRunner(response: UseCaseResponse<null>): DeleteEntryRunner {
    const spy = vi.fn<[string], Promise<UseCaseResponse<null>>>();
    spy.mockResolvedValue(response);

    const runner: DeleteEntryRunner = {
        run: spy
    };

    return runner;
}

describe('EntryElement', () => {
    beforeAll(() => {
        ensureCustomElementRegistered();
    });

    beforeEach(() => {
        document.body.innerHTML = '';
        vi.restoreAllMocks();
    });

    it('shows loading state immediately after connection', () => {
        const entry: GetEntryResponse = {
            id: '11111111-1111-1111-1111-111111111111',
            title: 'Title',
            body: 'Body',
            date: '2026-01-27',
            createdAt: '2026-01-27 18:51:07',
            updatedAt: ''
        };

        const getResponse = createGetSuccessResponse(entry);
        const getRunner = createFakeGetRunner(getResponse);
        const deleteRunner = createFakeDeleteRunner(createDeleteSuccessResponse());

        const element = new EntryElement(getRunner, deleteRunner);
        element.setAttribute('entry-id', entry.id);

        document.body.appendChild(element);

        const shadow = element.shadowRoot as ShadowRoot;
        const loading = shadow.querySelector('[data-testid="loading"]');

        expect(loading).not.toBeNull();
        expect(loading?.textContent).toContain('Loading entry');
    });

    it('renders entry data when UC-3 returns a successful response', async () => {
        const entry: GetEntryResponse = {
            id: '7eb72470-3240-4794-91bf-f7e999cf4ccf',
            title: 'Entry title',
            body: 'Entry body',
            date: '2026-01-27',
            createdAt: '2026-01-27 18:51:07',
            updatedAt: ''
        };

        const getResponse = createGetSuccessResponse(entry);
        const getRunner = createFakeGetRunner(getResponse);
        const deleteRunner = createFakeDeleteRunner(createDeleteSuccessResponse());

        const element = new EntryElement(getRunner, deleteRunner);
        element.setAttribute('entry-id', entry.id);

        document.body.appendChild(element);

        await Promise.resolve();
        await Promise.resolve();

        const shadow = element.shadowRoot as ShadowRoot;
        const loading = shadow.querySelector('[data-testid="loading"]');
        const error = shadow.querySelector('[data-testid="error"]');
        const data = shadow.querySelector('[data-testid="data"]');
        const edit = shadow.querySelector('[data-testid="edit"]');
        const del = shadow.querySelector('[data-testid="delete"]');
        const back = shadow.querySelector('[data-testid="back"]');

        expect(loading).toBeNull();
        expect(error).toBeNull();
        expect(data).not.toBeNull();

        expect(data?.textContent).toContain(entry.date);
        expect(data?.textContent).toContain(entry.body);

        expect(edit).not.toBeNull();
        expect(del).not.toBeNull();
        expect(back).not.toBeNull();

        expect(edit?.textContent).toContain('Edit');
        expect(del?.textContent).toContain('Delete');
        expect(back?.textContent).toContain('Back');
    });

    it('renders error state when UC-3 returns a failed response', async () => {
        const entryId = '7eb72470-3240-4794-91bf-f7e999cf4ccf';

        const errors = ['Entry not found'];
        const getResponse = createGetErrorResponse('ENTRY_NOT_FOUND', errors);
        const getRunner = createFakeGetRunner(getResponse);
        const deleteRunner = createFakeDeleteRunner(createDeleteSuccessResponse());

        const element = new EntryElement(getRunner, deleteRunner);
        element.setAttribute('entry-id', entryId);

        document.body.appendChild(element);

        await Promise.resolve();
        await Promise.resolve();

        const shadow = element.shadowRoot as ShadowRoot;
        const loading = shadow.querySelector('[data-testid="loading"]');
        const error = shadow.querySelector('[data-testid="error"]');

        expect(loading).toBeNull();
        expect(error).not.toBeNull();
        expect(error?.textContent).toContain('Entry not found');
    });

    it('triggers UC-4 delete flow when user confirms deletion', async () => {
        const entry: GetEntryResponse = {
            id: '7eb72470-3240-4794-91bf-f7e999cf4ccf',
            title: 'Entry title',
            body: 'Entry body',
            date: '2026-01-27',
            createdAt: '2026-01-27 18:51:07',
            updatedAt: ''
        };

        const getResponse = createGetSuccessResponse(entry);
        const getRunner = createFakeGetRunner(getResponse);

        const deleteResponse = createDeleteSuccessResponse();
        const deleteRunner = createFakeDeleteRunner(deleteResponse);

        const confirmSpy = vi.spyOn(window, 'confirm');
        confirmSpy.mockReturnValue(true);

        const element = new EntryElement(getRunner, deleteRunner);
        element.setAttribute('entry-id', entry.id);

        const deletedSpy = vi.fn();
        element.addEventListener('dl-entry-deleted', deletedSpy);

        document.body.appendChild(element);

        await Promise.resolve();
        await Promise.resolve();

        const shadow = element.shadowRoot as ShadowRoot;
        const del = shadow.querySelector('[data-testid="delete"]') as HTMLButtonElement;

        del.click();

        await Promise.resolve();
        await Promise.resolve();

        expect(deleteRunner.run).toHaveBeenCalledTimes(1);
        expect(deleteRunner.run).toHaveBeenCalledWith(entry.id);

        expect(deletedSpy).toHaveBeenCalledTimes(1);
        expect(deletedSpy.mock.calls[0][0].detail.id).toBe(entry.id);

        const deletedState = shadow.querySelector('[data-testid="deleted"]');
        expect(deletedState).not.toBeNull();
        expect(deletedState?.textContent).toContain('Entry deleted');
    });

    it('renders delete error when UC-4 returns a failed response', async () => {
        const entry: GetEntryResponse = {
            id: '7eb72470-3240-4794-91bf-f7e999cf4ccf',
            title: 'Entry title',
            body: 'Entry body',
            date: '2026-01-27',
            createdAt: '2026-01-27 18:51:07',
            updatedAt: ''
        };

        const getRunner = createFakeGetRunner(createGetSuccessResponse(entry));

        const errors = ['Delete failed'];
        const deleteRunner = createFakeDeleteRunner(
            createDeleteErrorResponse('DELETE_FAILED', errors)
        );

        const confirmSpy = vi.spyOn(window, 'confirm');
        confirmSpy.mockReturnValue(true);

        const element = new EntryElement(getRunner, deleteRunner);
        element.setAttribute('entry-id', entry.id);

        document.body.appendChild(element);

        await Promise.resolve();
        await Promise.resolve();

        const shadow = element.shadowRoot as ShadowRoot;
        const del = shadow.querySelector('[data-testid="delete"]') as HTMLButtonElement;

        del.click();

        await Promise.resolve();
        await Promise.resolve();

        const error = shadow.querySelector('[data-testid="error"]');
        expect(error).not.toBeNull();
        expect(error?.textContent).toContain('Delete failed');
    });

    it('renders generic delete error message when UC-4 runner throws', async () => {
        const entry: GetEntryResponse = {
            id: '7eb72470-3240-4794-91bf-f7e999cf4ccf',
            title: 'Entry title',
            body: 'Entry body',
            date: '2026-01-27',
            createdAt: '2026-01-27 18:51:07',
            updatedAt: ''
        };

        const getRunner = createFakeGetRunner(createGetSuccessResponse(entry));

        const deleteRunner: DeleteEntryRunner = {
            run: vi.fn().mockRejectedValue(new Error('Network broken'))
        };

        const confirmSpy = vi.spyOn(window, 'confirm');
        confirmSpy.mockReturnValue(true);

        const element = new EntryElement(getRunner, deleteRunner);
        element.setAttribute('entry-id', entry.id);

        document.body.appendChild(element);

        await Promise.resolve();
        await Promise.resolve();

        const shadow = element.shadowRoot as ShadowRoot;
        const del = shadow.querySelector('[data-testid="delete"]') as HTMLButtonElement;

        del.click();

        await Promise.resolve();
        await Promise.resolve();

        const error = shadow.querySelector('[data-testid="error"]');

        expect(error).not.toBeNull();
        expect(error?.textContent).toContain('Failed to delete entry');
    });
});
