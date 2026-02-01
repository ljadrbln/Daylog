/**
 * @file EditEntryElement.test.ts
 *
 * This test suite describes the behavior of the vanilla Web Component
 * <dl-entry-edit> for UC-5 (EditEntry), using UC-3 (GetEntry) for form prefill.
 *
 * Scenarios:
 * - shows loading state right after being connected to the DOM;
 * - renders prefilled form when UC-3 returns entry successfully;
 * - renders error state when UC-3 returns a failed response;
 * - renders generic error message when UC-3 runner throws;
 * - submits updated form values via UC-5 and emits dl-entry-updated on success;
 * - renders error state when UC-5 returns a failed response.
 */

import {describe, it, expect, vi, beforeAll, beforeEach} from 'vitest';
import {EditEntryElement} from '@src/Presentation/entries/vanilla/edit/EditEntryElement';
import type {UseCaseResponse} from '@src/Application/DTO/Common/UseCaseResponse';
import type {GetEntryResponse} from '@src/Application/DTO/Entries/GetEntry/GetEntryResponse';
import type {Entry} from '@src/Domain/Models/Entries/Entry';
import type {EntryRunner} from '@src/Presentation/entries/vanilla/view/EntryRunner';
import type {UpdateEntryRunner} from '@src/Presentation/entries/vanilla/edit/UpdateEntryRunner';

type UpdateParams = {
    id: string;
    title: string;
    body: string;
    date: string;
};

/**
 * Ensures that EditEntryElement constructor is registered
 * in the CustomElementRegistry before we instantiate it directly.
 *
 * jsdom requires the constructor to be part of the registry;
 * otherwise "Invalid constructor" is thrown.
 *
 * @returns {void}
 */
function ensureCustomElementRegistered(): void {
    const tagName = 'dl-entry-edit';
    const existing = customElements.get(tagName);

    if (!existing) {
        const ctor = EditEntryElement;
        customElements.define(tagName, ctor);
    }
}

/**
 * Creates a successful use-case response with provided data.
 *
 * @template T
 * @param {T} data Response payload.
 *
 * @returns {UseCaseResponse<T>} Success response with data.
 */
function createSuccessResponse<T>(data: T): UseCaseResponse<T> {
    const response: UseCaseResponse<T> = {
        success: true,
        code: null,
        status: 200,
        data,
        errors: null
    };

    return response;
}

/**
 * Creates a failed use-case response with provided errors.
 *
 * @template T
 * @param {string} code Machine-readable error code.
 * @param {string[]} errors Human-readable errors.
 *
 * @returns {UseCaseResponse<T>} Failed response without data.
 */
function createErrorResponse<T>(code: string, errors: string[]): UseCaseResponse<T> {
    const response: UseCaseResponse<T> = {
        success: false,
        code,
        status: 500,
        data: null,
        errors
    };

    return response;
}

/**
 * Flushes pending microtasks created by async callbacks inside components.
 *
 * @returns {Promise<void>} Promise resolved after several microtask ticks.
 */
async function flushMicrotasks(): Promise<void> {
    await Promise.resolve();
    await Promise.resolve();
}

/**
 * Creates a fake GetEntry runner that always resolves with the provided response.
 *
 * @param {UseCaseResponse<GetEntryResponse>} response Predefined response.
 *
 * @returns {EntryRunner} Runner whose run() resolves with the response.
 */
function createFakeGetRunner(
    response: UseCaseResponse<GetEntryResponse>
): EntryRunner {
    const spy = vi.fn<[string], Promise<UseCaseResponse<GetEntryResponse>>>();
    spy.mockResolvedValue(response);

    const runner: EntryRunner = {
        run: spy
    };

    return runner;
}

/**
 * Creates a fake UpdateEntry runner that always resolves with the provided response.
 *
 * @param {UseCaseResponse<Entry>} response Predefined response.
 *
 * @returns {UpdateEntryRunner} Runner whose run() resolves with the response.
 */
function createFakeUpdateRunner(
    response: UseCaseResponse<Entry>
): UpdateEntryRunner {
    const spy = vi.fn<[UpdateParams], Promise<UseCaseResponse<Entry>>>();
    spy.mockResolvedValue(response);

    const runner: UpdateEntryRunner = {
        run: spy
    };

    return runner;
}

/**
 * Sets entry-id attribute on the element.
 *
 * @param {EditEntryElement} element Custom element instance.
 * @param {string} id Entry identifier.
 *
 * @returns {void}
 */
function setEntryId(element: EditEntryElement, id: string): void {
    element.setAttribute('entry-id', id);
}

/**
 * Reads form nodes from the component shadow root.
 *
 * @param {ShadowRoot} shadow Component shadow root.
 *
 * @returns {{form: HTMLFormElement; title: HTMLInputElement; date: HTMLInputElement; body: HTMLTextAreaElement}}
 */
function getFormNodes(shadow: ShadowRoot): {
    form: HTMLFormElement;
    title: HTMLInputElement;
    date: HTMLInputElement;
    body: HTMLTextAreaElement;
} {
    const formNode = shadow.querySelector('form[data-testid="form"]');
    const titleNode = shadow.querySelector('input[name="title"]');
    const dateNode = shadow.querySelector('input[name="date"]');
    const bodyNode = shadow.querySelector('textarea[name="body"]');

    if (!(formNode instanceof HTMLFormElement)) {
        throw new Error('Form node is missing.');
    }

    if (!(titleNode instanceof HTMLInputElement)) {
        throw new Error('Title input is missing.');
    }

    if (!(dateNode instanceof HTMLInputElement)) {
        throw new Error('Date input is missing.');
    }

    if (!(bodyNode instanceof HTMLTextAreaElement)) {
        throw new Error('Body textarea is missing.');
    }

    const nodes = {
        form: formNode,
        title: titleNode,
        date: dateNode,
        body: bodyNode
    };

    return nodes;
}

describe('EditEntryElement', () => {
    beforeAll(() => {
        ensureCustomElementRegistered();
    });

    beforeEach(() => {
        document.body.innerHTML = '';
    });

    it('shows loading state immediately after connection', () => {
        const entry: GetEntryResponse = {
            id: '11111111-1111-1111-1111-111111111111',
            title: 'Prefilled title',
            body: 'Prefilled body',
            date: '2026-01-01'
        };

        const getResponse = createSuccessResponse(entry);
        const getRunner = createFakeGetRunner(getResponse);

        const updateResponse = createSuccessResponse(entry as unknown as Entry);
        const updateRunner = createFakeUpdateRunner(updateResponse);

        const element = new EditEntryElement(getRunner, updateRunner);

        const id = '11111111-1111-1111-1111-111111111111';
        setEntryId(element, id);

        document.body.appendChild(element);

        const shadow = element.shadowRoot as ShadowRoot;
        const loading = shadow.querySelector('[data-testid="loading"]');

        expect(loading).not.toBeNull();
        expect(loading?.textContent).toContain('Loading entry');
    });

    it('renders prefilled form when UC-3 returns entry successfully', async () => {
        const entry: GetEntryResponse = {
            id: '11111111-1111-1111-1111-111111111111',
            title: 'Prefilled title',
            body: 'Prefilled body',
            date: '2026-01-01'
        };

        const getResponse = createSuccessResponse(entry);
        const getRunner = createFakeGetRunner(getResponse);

        const updateResponse = createSuccessResponse(entry as unknown as Entry);
        const updateRunner = createFakeUpdateRunner(updateResponse);

        const element = new EditEntryElement(getRunner, updateRunner);

        const id = '11111111-1111-1111-1111-111111111111';
        setEntryId(element, id);

        document.body.appendChild(element);

        await flushMicrotasks();

        const shadow = element.shadowRoot as ShadowRoot;

        const loading = shadow.querySelector('[data-testid="loading"]');
        const idle = shadow.querySelector('[data-testid="idle"]');
        const error = shadow.querySelector('[data-testid="error"]');

        expect(loading).toBeNull();
        expect(error).toBeNull();
        expect(idle).not.toBeNull();

        const nodes = getFormNodes(shadow);

        expect(nodes.title.value).toBe('Prefilled title');
        expect(nodes.date.value).toBe('2026-01-01');
        expect(nodes.body.value).toContain('Prefilled body');
    });

    it('renders error state when UC-3 returns a failed response', async () => {
        const errors = ['Entry not found'];
        const getResponse = createErrorResponse<GetEntryResponse>('ENTRY_NOT_FOUND', errors);
        const getRunner = createFakeGetRunner(getResponse);

        const updateResponse = createSuccessResponse({} as unknown as Entry);
        const updateRunner = createFakeUpdateRunner(updateResponse);

        const element = new EditEntryElement(getRunner, updateRunner);

        const id = '99999999-9999-9999-9999-999999999999';
        setEntryId(element, id);

        document.body.appendChild(element);

        await flushMicrotasks();

        const shadow = element.shadowRoot as ShadowRoot;

        const loading = shadow.querySelector('[data-testid="loading"]');
        const error = shadow.querySelector('[data-testid="error"]');

        expect(loading).toBeNull();
        expect(error).not.toBeNull();
        expect(error?.textContent).toContain('Entry not found');
    });

    it('renders generic error message when UC-3 runner throws', async () => {
        const failingGetRunner: EntryRunner = {
            run: vi.fn().mockRejectedValue(new Error('Network broken'))
        };

        const updateResponse = createSuccessResponse({} as unknown as Entry);
        const updateRunner = createFakeUpdateRunner(updateResponse);

        const element = new EditEntryElement(failingGetRunner, updateRunner);

        const id = '11111111-1111-1111-1111-111111111111';
        setEntryId(element, id);

        document.body.appendChild(element);

        await flushMicrotasks();

        const shadow = element.shadowRoot as ShadowRoot;
        const error = shadow.querySelector('[data-testid="error"]');

        expect(error).not.toBeNull();
        expect(error?.textContent).toContain('Failed to load entry');
    });

    it('submits updated values via UC-5 and emits dl-entry-updated on success', async () => {
        const entry: GetEntryResponse = {
            id: '11111111-1111-1111-1111-111111111111',
            title: 'Prefilled title',
            body: 'Prefilled body',
            date: '2026-01-01'
        };

        const getResponse = createSuccessResponse(entry);
        const getRunner = createFakeGetRunner(getResponse);

        const updatedEntry: Entry = {
            id: '11111111-1111-1111-1111-111111111111',
            title: 'Updated title',
            body: 'Updated body',
            date: '2026-01-02',
            createdAt: '2026-01-01T10:00:00Z',
            updatedAt: '2026-01-02T10:00:00Z'
        };

        const updateResponse = createSuccessResponse(updatedEntry);
        const updateRunner = createFakeUpdateRunner(updateResponse);

        const element = new EditEntryElement(getRunner, updateRunner);

        const id = '11111111-1111-1111-1111-111111111111';
        setEntryId(element, id);

        const eventSpy = vi.fn<[CustomEvent], void>();

        element.addEventListener('dl-entry-updated', (event: Event) => {
            const custom = event as CustomEvent;
            eventSpy(custom);
        });

        document.body.appendChild(element);

        await flushMicrotasks();

        const shadow = element.shadowRoot as ShadowRoot;
        const nodes = getFormNodes(shadow);

        nodes.title.value = 'Updated title';
        nodes.date.value = '2026-01-02';
        nodes.body.value = 'Updated body';

        nodes.form.dispatchEvent(new Event('submit', {bubbles: true, cancelable: true}));

        const submitting = shadow.querySelector('[data-testid="submitting"]');

        expect(submitting).not.toBeNull();

        await flushMicrotasks();

        const success = shadow.querySelector('[data-testid="success"]');

        expect(success).not.toBeNull();

        expect(eventSpy).toHaveBeenCalledTimes(1);

        const eventArg = eventSpy.mock.calls[0][0];
        expect(eventArg.detail).toEqual({id});

        const updateFn = updateRunner.run as unknown as ReturnType<typeof vi.fn>;
        expect(updateFn).toHaveBeenCalledTimes(1);

        const calledParams = updateFn.mock.calls[0][0] as UpdateParams;

        expect(calledParams).toEqual({
            id,
            title: 'Updated title',
            body: 'Updated body',
            date: '2026-01-02'
        });
    });

    it('renders error state when UC-5 returns a failed response', async () => {
        const entry: GetEntryResponse = {
            id: '11111111-1111-1111-1111-111111111111',
            title: 'Prefilled title',
            body: 'Prefilled body',
            date: '2026-01-01'
        };

        const getResponse = createSuccessResponse(entry);
        const getRunner = createFakeGetRunner(getResponse);

        const errors = ['Validation failed'];
        const updateResponse = createErrorResponse<Entry>('UPDATE_FAILED', errors);
        const updateRunner = createFakeUpdateRunner(updateResponse);

        const element = new EditEntryElement(getRunner, updateRunner);

        const id = '11111111-1111-1111-1111-111111111111';
        setEntryId(element, id);

        document.body.appendChild(element);

        await flushMicrotasks();

        const shadow = element.shadowRoot as ShadowRoot;
        const nodes = getFormNodes(shadow);

        nodes.title.value = 'Updated title';
        nodes.date.value = '2026-01-02';
        nodes.body.value = 'Updated body';

        nodes.form.dispatchEvent(new Event('submit', {bubbles: true, cancelable: true}));

        await flushMicrotasks();

        const error = shadow.querySelector('[data-testid="error"]');

        expect(error).not.toBeNull();
        expect(error?.textContent).toContain('Validation failed');
    });
});
