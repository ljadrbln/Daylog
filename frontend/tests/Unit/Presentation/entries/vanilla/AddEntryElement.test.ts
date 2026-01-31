/**
 * @file AddEntryElement.test.ts
 *
 * This test suite describes the behavior of the vanilla Web Component
 * <dl-entry-add> for UC-1 (AddEntry).
 *
 * Scenarios:
 * - renders form immediately after being connected to the DOM;
 * - submits form and renders success state when UC-1 succeeds;
 * - renders validation errors when UC-1 returns success === false;
 * - renders a generic error message when the runner throws.
 */

import {describe, it, expect, vi, beforeAll, beforeEach} from 'vitest';
import {
    AddEntryElement,
    type AddEntryRunner
} from '@src/Presentation/entries/vanilla/add/AddEntryElement';
import type {AddEntryResponse} from '@src/Application/DTO/Entries/AddEntry/AddEntryResponse';
import type {Entry} from '@src/Domain/Models/Entries/Entry';

/**
 * Ensures that AddEntryElement constructor is registered
 * in the CustomElementRegistry before we instantiate it directly.
 *
 * @returns {void}
 */
function ensureCustomElementRegistered(): void {
    const tagName = 'dl-entry-add';
    const existing = customElements.get(tagName);

    if (!existing) {
        const ctor = AddEntryElement;
        customElements.define(tagName, ctor);
    }
}

/**
 * Creates a valid Entry object for tests.
 *
 * @returns {Entry}
 */
function createEntry(): Entry {
    const entry = {
        id: '11111111-1111-1111-1111-111111111111',
        title: 'Test title',
        body: 'Test body',
        date: '2025-01-01',
        createdAt: '2025-01-01T10:00:00Z',
        updatedAt: '2025-01-01T10:00:00Z'
    } as Entry;

    return entry;
}

/**
 * Wraps Entry into a successful UC-1 response.
 */
function createSuccessResponse(entry: Entry): AddEntryResponse {
    const response: AddEntryResponse = {
        success: true,
        code: null,
        status: 200,
        data: entry,
        errors: null
    };

    return response;
}

/**
 * Wraps validation errors into a failed UC-1 response.
 */
function createErrorResponse(errors: string[]): AddEntryResponse {
    const response: AddEntryResponse = {
        success: false,
        code: 'ADD_ENTRY_FAILED',
        status: 422,
        data: null,
        errors
    };

    return response;
}

/**
 * Creates a fake runner that resolves with a predefined response.
 */
function createFakeRunner(response: AddEntryResponse): AddEntryRunner {
    const spy = vi.fn<[{title: string; body: string; date: string}], Promise<AddEntryResponse>>();

    spy.mockResolvedValue(response);

    const runner: AddEntryRunner = {
        run: spy
    };

    return runner;
}

describe('AddEntryElement', () => {
    beforeAll(() => {
        ensureCustomElementRegistered();
    });

    beforeEach(() => {
        document.body.innerHTML = '';
    });

    it('renders form immediately after connection', () => {
        const runner = createFakeRunner(createSuccessResponse(createEntry()));
        const element = new AddEntryElement(runner);

        document.body.appendChild(element);

        const shadow = element.shadowRoot as ShadowRoot;
        const idle = shadow.querySelector('[data-testid="idle"]');

        expect(idle).not.toBeNull();
    });

    it('submits form and renders success state when UC-1 succeeds', async () => {
        const entry = createEntry();
        const runner = createFakeRunner(createSuccessResponse(entry));
        const element = new AddEntryElement(runner);

        document.body.appendChild(element);

        const shadow = element.shadowRoot as ShadowRoot;

        const title = shadow.querySelector('input[name="title"]') as HTMLInputElement;
        const body = shadow.querySelector('textarea[name="body"]') as HTMLTextAreaElement;
        const date = shadow.querySelector('input[name="date"]') as HTMLInputElement;

        title.value = 'My title';
        body.value = 'My body';
        date.value = '2025-01-01';

        const form = shadow.querySelector('form') as HTMLFormElement;
        form.dispatchEvent(new Event('submit', {bubbles: true, cancelable: true}));

        await Promise.resolve();
        await Promise.resolve();

        const success = shadow.querySelector('[data-testid="success"]');

        expect(runner.run).toHaveBeenCalledOnce();
        expect(success).not.toBeNull();
        expect(success?.textContent).toContain('Entry created successfully');
    });

    it('renders validation error when UC-1 returns success === false', async () => {
        const errors = ['TITLE_REQUIRED'];
        const runner = createFakeRunner(createErrorResponse(errors));
        const element = new AddEntryElement(runner);

        document.body.appendChild(element);

        const shadow = element.shadowRoot as ShadowRoot;
        const form = shadow.querySelector('form') as HTMLFormElement;

        form.dispatchEvent(new Event('submit', {bubbles: true, cancelable: true}));

        await Promise.resolve();
        await Promise.resolve();

        const error = shadow.querySelector('[data-testid="error"]');

        expect(error).not.toBeNull();
        expect(error?.textContent).toContain('TITLE_REQUIRED');
    });

    it('renders generic error message when runner throws', async () => {
        const failingRunner: AddEntryRunner = {
            run: vi.fn().mockRejectedValue(new Error('Network broken'))
        };

        const element = new AddEntryElement(failingRunner);

        document.body.appendChild(element);

        const shadow = element.shadowRoot as ShadowRoot;
        const form = shadow.querySelector('form') as HTMLFormElement;

        form.dispatchEvent(new Event('submit', {bubbles: true, cancelable: true}));

        await Promise.resolve();
        await Promise.resolve();

        const error = shadow.querySelector('[data-testid="error"]');

        expect(error).not.toBeNull();
        expect(error?.textContent).toContain('Failed to create entry');
    });
});
