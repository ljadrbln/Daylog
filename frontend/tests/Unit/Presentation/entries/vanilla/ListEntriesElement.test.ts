/**
 * @file ListEntriesElement.test.ts
 *
 * This test suite describes the behavior of the vanilla Web Component
 * <dl-list-entries> for UC-2 (ListEntries).
 *
 * Scenarios:
 * - shows loading state right after being connected to the DOM;
 * - renders entries list when UC-2 returns a successful response;
 * - renders error from the use-case response when success === false;
 * - renders a generic error message when the runner throws.
 */

import {describe, it, expect, vi, beforeAll, beforeEach} from 'vitest';
import {
    ListEntriesElement,
    type ListEntriesRunner
} from '@src/Presentation/entries/vanilla/ListEntriesElement';
import type {UseCaseResponse} from '@src/Application/DTO/Common/UseCaseResponse';
import type {ListEntriesPageInterface} from '@src/Domain/Interfaces/Entries/ListEntriesPageInterface';

/**
 * Ensures that ListEntriesElement constructor is registered
 * in the CustomElementRegistry before we instantiate it directly.
 *
 * jsdom requires the constructor to be part of the registry;
 * otherwise "Invalid constructor" is thrown.
 *
 * @returns {void}
 */
function ensureCustomElementRegistered(): void {
    const tagName = 'dl-list-entries';
    const existing = customElements.get(tagName);

    if (!existing) {
        const ctor = ListEntriesElement;
        customElements.define(tagName, ctor);
    }
}

/**
 * Creates an empty ListEntriesPageInterface instance for baseline tests.
 *
 * @returns {ListEntriesPageInterface} Page object with zero items and default pagination.
 */
function createEmptyPage(): ListEntriesPageInterface {
    const page: ListEntriesPageInterface = {
        items: [],
        page: 1,
        perPage: 20,
        total: 0,
        pagesCount: 0
    };

    return page;
}

/**
 * Wraps given page into a successful use-case response.
 *
 * @param {ListEntriesPageInterface} page Page data returned by UC-2.
 *
 * @returns {UseCaseResponse<ListEntriesPageInterface>} Successful response with provided data.
 */
function createSuccessResponse(
    page: ListEntriesPageInterface
): UseCaseResponse<ListEntriesPageInterface> {
    const response: UseCaseResponse<ListEntriesPageInterface> = {
        success: true,
        code: null,
        status: 200,
        data: page,
        errors: null
    };

    return response;
}

/**
 * Wraps error information into a failed use-case response.
 *
 * @param {string} code Machine-readable error code.
 * @param {string[]} errors Human-readable error messages.
 *
 * @returns {UseCaseResponse<ListEntriesPageInterface>} Failed response without data.
 */
function createErrorResponse(
    code: string,
    errors: string[]
): UseCaseResponse<ListEntriesPageInterface> {
    const response: UseCaseResponse<ListEntriesPageInterface> = {
        success: false,
        code,
        status: 500,
        data: null,
        errors
    };

    return response;
}

/**
 * Creates a fake runner that always resolves with the provided response.
 *
 * @param {UseCaseResponse<ListEntriesPageInterface>} response Predefined response.
 *
 * @returns {ListEntriesRunner} Runner whose run() method resolves with the response.
 */
function createFakeRunner(
    response: UseCaseResponse<ListEntriesPageInterface>
): ListEntriesRunner {
    const spy = vi.fn<[], Promise<UseCaseResponse<ListEntriesPageInterface>>>();

    spy.mockResolvedValue(response);

    const runner: ListEntriesRunner = {
        run: spy
    };

    return runner;
}

describe('ListEntriesElement', () => {
    beforeAll(() => {
        ensureCustomElementRegistered();
    });

    beforeEach(() => {
        document.body.innerHTML = '';
    });

    it('shows loading state immediately after connection', () => {
        const page = createEmptyPage();
        const response = createSuccessResponse(page);
        const runner = createFakeRunner(response);

        const element = new ListEntriesElement(runner);

        document.body.appendChild(element);

        const shadow = element.shadowRoot as ShadowRoot;
        const loading = shadow.querySelector('[data-testid="loading"]');

        expect(loading).not.toBeNull();
        expect(loading?.textContent).toContain('Loading entries');
    });

    it('renders entries list when UC-2 returns items successfully', async () => {
        const firstEntry = {
            id: '11111111-1111-1111-1111-111111111111',
            title: 'First entry title',
            body: 'First entry body',
            date: '2025-12-01',
            createdAt: '2025-12-01T10:00:00Z',
            updatedAt: '2025-12-01T10:00:00Z'
        };

        const secondEntry = {
            id: '22222222-2222-2222-2222-222222222222',
            title: 'Second entry title',
            body: 'Second entry body',
            date: '2025-12-02',
            createdAt: '2025-12-02T10:00:00Z',
            updatedAt: '2025-12-02T10:30:00Z'
        };

        const page: ListEntriesPageInterface = {
            items: [firstEntry, secondEntry],
            page: 1,
            perPage: 20,
            total: 2,
            pagesCount: 1
        };

        const response = createSuccessResponse(page);
        const runner = createFakeRunner(response);

        const element = new ListEntriesElement(runner);

        document.body.appendChild(element);

        // allow pending microtasks (Promise chains) to resolve
        await Promise.resolve();
        await Promise.resolve();

        const shadow = element.shadowRoot as ShadowRoot;
        const loading = shadow.querySelector('[data-testid="loading"]');
        const error = shadow.querySelector('[data-testid="error"]');
        const items = shadow.querySelectorAll('[data-testid="entry-item"]');

        expect(loading).toBeNull();
        expect(error).toBeNull();
        expect(items).toHaveLength(2);
        expect(items[0].textContent).toContain('First entry title');
        expect(items[1].textContent).toContain('Second entry title');
    });

    it('renders error state when UC-2 returns a failed response', async () => {
        const errors = ['Unexpected error while listing entries'];
        const response = createErrorResponse('LIST_ENTRIES_FAILED', errors);
        const runner = createFakeRunner(response);

        const element = new ListEntriesElement(runner);

        document.body.appendChild(element);

        await Promise.resolve();
        await Promise.resolve();

        const shadow = element.shadowRoot as ShadowRoot;
        const loading = shadow.querySelector('[data-testid="loading"]');
        const error = shadow.querySelector('[data-testid="error"]');

        expect(loading).toBeNull();
        expect(error).not.toBeNull();
        expect(error?.textContent).toContain('Unexpected error while listing entries');
    });

    it('renders generic error message when UC-2 runner throws', async () => {
        const failingRunner: ListEntriesRunner = {
            run: vi.fn().mockRejectedValue(new Error('Network broken'))
        };

        const element = new ListEntriesElement(failingRunner);

        document.body.appendChild(element);

        await Promise.resolve();
        await Promise.resolve();

        const shadow = element.shadowRoot as ShadowRoot;
        const loading = shadow.querySelector('[data-testid="loading"]');
        const error = shadow.querySelector('[data-testid="error"]');

        expect(loading).toBeNull();
        expect(error).not.toBeNull();
        expect(error?.textContent).toContain('Failed to load entries');
    });
});
