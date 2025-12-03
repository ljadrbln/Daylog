/**
 * Vanilla Web Component for UC-2 (ListEntries).
 *
 * Purpose:
 * - Execute UC-2 via UseCaseRunner + ListEntries provider.
 * - Render three visual states via shadow DOM:
 *   loading, data, error.
 *
 * Notes:
 * - The component depends only on application/domain-level abstractions:
 *   UseCaseRunner, UseCaseResponse, ListEntriesPageInterface.
 * - It does not know anything about HTTP or repositories.
 */

import type {UseCaseResponse} from '@src/Application/DTO/Common/UseCaseResponse';
import type {ListEntriesPageInterface} from '@src/Domain/Interfaces/Entries/ListEntriesPageInterface';
import {UseCaseRunner} from '@src/Presentation/runner/UseCaseRunner';
import {makeListEntriesUseCase} from '@src/Configuration/Providers/Entries/ListEntriesProvider';

/**
 * Minimal runner interface used by the component.
 * Real implementation is built from UseCaseRunner + ListEntries use case.
 * Tests inject a fake implementation.
 */
export interface ListEntriesRunner {
    run: () => Promise<UseCaseResponse<ListEntriesPageInterface>>;
}

/**
 * Create ListEntriesRunner backed by UseCaseRunner and ListEntries use case provider.
 *
 * @returns {ListEntriesRunner} Runner that executes UC-2 ListEntries.
 */
function createListEntriesRunner(): ListEntriesRunner {
    const useCase = makeListEntriesUseCase();

    const listEntriesRunner: ListEntriesRunner = {
        run: async (): Promise<UseCaseResponse<ListEntriesPageInterface>> => {
            const params = {};

            const runnerResult = await UseCaseRunner.run<typeof params, ListEntriesPageInterface>(
                useCase,
                params
            );

            const response = runnerResult as UseCaseResponse<ListEntriesPageInterface>;

            return response;
        }
    };

    return listEntriesRunner;
}

/**
 * Web Component <dl-list-entries> for UC-2 ListEntries.
 *
 * Scenarios:
 * - On connection, shows loading state and triggers UC-2.
 * - On success, renders a list of entries.
 * - On failure or exception, renders error message.
 *
 * The public contract is the custom element itself; internal wiring
 * is hidden behind ListEntriesRunner.
 */
export class ListEntriesElement extends HTMLElement {
    private readonly runner: ListEntriesRunner;

    public constructor(runner?: ListEntriesRunner) {
        super();

        const shadow = this.attachShadow({mode: 'open'});

        this.runner = runner ?? createListEntriesRunner();

        this.renderLoading(shadow);
    }

    public connectedCallback(): void {
        const shadow = this.shadowRoot as ShadowRoot;

        void this.loadEntries(shadow);
    }

    /**
     * Execute UC-2 and switch between loading/data/error states.
     *
     * @param {ShadowRoot} shadow Shadow root used for rendering.
     *
     * @returns {Promise<void>} Promise that resolves when rendering is complete.
     */
    private async loadEntries(shadow: ShadowRoot): Promise<void> {
        this.renderLoading(shadow);

        try {
            const response = await this.runner.run();

            if (response.success && response.data) {
                this.renderData(shadow, response.data);

                return;
            }

            const message = this.extractErrorMessage(response);
            this.renderError(shadow, message);
        } catch (error) {
            const fallback = 'Failed to load entries. Please try again.';
            this.renderError(shadow, fallback);
        }
    }

    /**
     * Extract the most relevant error message from UseCaseResponse.
     *
     * @param {UseCaseResponse<ListEntriesPageInterface>} response UC-2 response.
     *
     * @returns {string} Human-readable error message.
     */
    private extractErrorMessage(response: UseCaseResponse<ListEntriesPageInterface>): string {
        if (Array.isArray(response.errors) && response.errors.length > 0) {
            const message = response.errors[0];

            return message;
        }

        const fallback = 'Failed to load entries. Please try again.';

        return fallback;
    }

    /**
     * Render loading state into shadow DOM.
     *
     * @param {ShadowRoot} shadow Shadow root for the component.
     *
     * @returns {void}
     */
    private renderLoading(shadow: ShadowRoot): void {
        const html = `
            <style>
                :host {
                    display: block;
                    font-family: system-ui, -apple-system, BlinkMacSystemFont,
                        "Segoe UI", sans-serif;
                }

                .dl-list-entries__loading {
                    padding: 0.5rem 0;
                    font-size: 0.9rem;
                    opacity: 0.75;
                }
            </style>
            <div class="dl-list-entries__loading" data-testid="loading">
                Loading entries...
            </div>
        `;

        shadow.innerHTML = html;
    }

    /**
     * Render error state into shadow DOM.
     *
     * @param {ShadowRoot} shadow Shadow root for the component.
     * @param {string} message Error message to show.
     *
     * @returns {void}
     */
    private renderError(shadow: ShadowRoot, message: string): void {
        const html = `
            <style>
                :host {
                    display: block;
                    font-family: system-ui, -apple-system, BlinkMacSystemFont,
                        "Segoe UI", sans-serif;
                }

                .dl-list-entries__error {
                    padding: 0.5rem 0;
                    font-size: 0.9rem;
                    color: #b00020;
                }
            </style>
            <div class="dl-list-entries__error" data-testid="error">
                ${message}
            </div>
        `;

        shadow.innerHTML = html;
    }

    /**
     * Render list of entries into shadow DOM.
     *
     * @param {ShadowRoot} shadow Shadow root for the component.
     * @param {ListEntriesPageInterface} page Page payload returned by UC-2.
     *
     * @returns {void}
     */
    private renderData(shadow: ShadowRoot, page: ListEntriesPageInterface): void {
        const itemsHtml = page.items
            .map((entry, index): string => {
                const baseIndex = (page.page - 1) * page.perPage;
                const number = baseIndex + index + 1;

                const numberText = `№ ${number}. ${entry.date}`;
                const titleText = entry.title;
                const bodyText = entry.body;

                const itemHtml = `
                    <li class="dl-list-entries__item" data-testid="entry-item">
                        <div class="dl-list-entries__item-header">
                            <span class="dl-list-entries__item-number">${numberText}</span>
                            <button class="dl-list-entries__view-button" type="button">
                                Просмотреть
                            </button>
                        </div>

                        <p class="dl-list-entries__item-title">${titleText}</p>
                        <p class="dl-list-entries__item-body">${bodyText}</p>
                    </li>
                `;

                return itemHtml;
            })
            .join('');

        const html = `
            <style>
                :host {
                    display: block;
                    font-family: system-ui, -apple-system, BlinkMacSystemFont,
                        "Segoe UI", sans-serif;
                }

                .dl-list-entries__items {
                    list-style: none;
                    padding: 0;
                    margin: 0.5rem 0 0;
                }

                .dl-list-entries__item {
                    padding: 0.75rem 1rem;
                    border-bottom: 1px solid #ddd;
                }

                .dl-list-entries__item-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 0.5rem;
                }

                .dl-list-entries__item-number {
                    font-weight: 600;
                }

                .dl-list-entries__view-button {
                    font-size: 0.85rem;
                    padding: 0.25rem 0.75rem;
                    border-radius: 4px;
                    border: 1px solid #e0e0e0;
                    background: #f5f5f5;
                    cursor: pointer;
                }

                .dl-list-entries__item-title {
                    margin: 0 0 0.25rem;
                    font-weight: 500;
                }

                .dl-list-entries__item-body {
                    margin: 0;
                    font-size: 0.9rem;
                }
            </style>
            <ul class="dl-list-entries__items">
                ${itemsHtml}
            </ul>
        `;

        shadow.innerHTML = html;
    }

}
