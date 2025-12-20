/**
 * Vanilla Web Component for UC-2 (ListEntries).
 *
 * Purpose:
 * - Execute UC-2 via UseCaseRunner + ListEntries provider.
 * - Render three UI states inside shadow DOM: loading, error, data.
 *
 * Mechanics:
 * - A shared stylesheet <link> (/assets/css/dl-components.css) is appended once
 *   and kept in shadow root.
 * - All render methods update only a dedicated root container, so the stylesheet
 *   is never removed during re-rendering.
 *
 * Notes:
 * - The component depends on runner/provider wiring only.
 * - It does not access HTTP or repositories directly.
 */

import type {UseCaseResponse} from '@src/Application/DTO/Common/UseCaseResponse';
import type {ListEntriesPageInterface} from '@src/Domain/Interfaces/Entries/ListEntriesPageInterface';
import type {ListEntriesRunner} from './ListEntriesRunner';
import {createListEntriesRunner} from './ListEntriesRunner';

export class ListEntriesElement extends HTMLElement {
    private readonly runner: ListEntriesRunner;
    private readonly root: HTMLDivElement;

    public constructor(runner?: ListEntriesRunner) {
        super();

        const shadow = this.attachShadow({mode: 'open'});

        this.runner = runner ?? createListEntriesRunner();

        this.loadStyles(shadow);

        this.root = document.createElement('div');
        shadow.appendChild(this.root);
    }

    public connectedCallback(): void {
        this.renderLoading();

        this.run();
    }

    private async run(): Promise<void> {
        try {
            const response = await this.runner.run();

            if (response.success) {
                const data = response.data;

                if (!data) {
                    const message = 'Failed to load entries. Please try again.';
                    this.renderError(message);

                    return;
                }

                this.renderData(data);

                return;
            }

            const message = this.pickErrorMessage(response);

            this.renderError(message);
        } catch (error) {
            const message = this.normalizeError(error);

            this.renderError(message);
        }
    }

    private loadStyles(shadow: ShadowRoot): void {
        const href = '/assets/css/dl-components.css';

        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = href;

        shadow.appendChild(link);
    }

    private normalizeError(error: unknown): string {
        if (error instanceof Error) {
            const message = error.message;
            return message;
        }

        const message = 'Failed to load entries. Please try again.';
        return message;
    }

    private pickErrorMessage(response: UseCaseResponse<ListEntriesPageInterface>): string {
        const errors = response.errors;

        if (errors && errors.length) {
            const message = errors.join(', ');
            return message;
        }

        const fallback = 'Failed to load entries. Please try again.';

        return fallback;
    }

    private renderLoading(): void {
        const html = `
            <div class="dl-list-entries container" data-testid="loading">
                <div class="box">
                    <progress class="progress is-small is-primary" max="100">Loading</progress>
                    <p class="has-text-grey">Loading entries&hellip;</p>
                </div>
            </div>
        `;

        this.root.innerHTML = html;
    }

    private renderError(message: string): void {
        const html = `
            <div class="dl-list-entries container" data-testid="error">
                <div class="notification is-danger is-light">
                    ${message}
                </div>
            </div>
        `;

        this.root.innerHTML = html;
    }

    private renderData(page: ListEntriesPageInterface): void {
        if (!page.items.length) {
            const html = `
                <div class="dl-list-entries container" data-testid="empty">
                    <div class="notification is-warning is-light">
                        Записей не найдено.
                    </div>
                </div>
            `;

            this.root.innerHTML = html;

            return;
        }

        const itemsHtml = page.items
            .map((entry, index): string => {
                const baseIndex = (page.page - 1) * page.perPage;
                const number = baseIndex + index + 1;

                const numberText = `№ ${number}. ${entry.date}`;
                const titleText = entry.title;
                const bodyText = entry.body;

                const itemHtml = `
                    <div class="box" data-testid="entry-item">
                        <div class="is-flex is-justify-content-space-between is-align-items-center">
                            <span class="has-text-grey">${numberText}</span>
                            <button class="button is-link is-small" type="button">
                                Просмотреть
                            </button>
                        </div>

                        <p class="title is-6 mt-3">${titleText}</p>
                        <div class="content">
                            <p>${bodyText}</p>
                        </div>
                    </div>
                `;

                return itemHtml;
            })
            .join('');

        const html = `
            <div class="dl-list-entries container">
                ${itemsHtml}
            </div>
        `;

        this.root.innerHTML = html;
    }
}
