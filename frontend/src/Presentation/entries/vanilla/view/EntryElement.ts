/**
 * Vanilla Web Component for UC-3 (GetEntry).
 *
 * Purpose:
 * - Execute UC-3 via UseCaseRunner + GetEntry provider.
 * - Render three UI states inside shadow DOM: loading, error, data.
 *
 * Mechanics:
 * - A shared stylesheet <link> (/assets/css/dl-components.css) is appended once
 *   and kept in shadow root.
 * - All render methods update only a dedicated root container, so the stylesheet
 *   is never removed during re-rendering.
 * - Entry id is read from the `entry-id` attribute. On changes, the component
 *   re-runs the use case and re-renders.
 *
 * Notes:
 * - The component depends on runner/provider wiring only.
 * - It does not access HTTP or repositories directly.
 */

import type {UseCaseResponse} from '@src/Application/DTO/Common/UseCaseResponse';
import type {GetEntryResponse} from '@src/Application/DTO/Entries/GetEntry/GetEntryResponse';
import type {EntryRunner} from './EntryRunner';
import {createEntryRunner} from './EntryRunner';

export class EntryElement extends HTMLElement {
    private static readonly entryIdAttribute = 'entry-id';

    private readonly runner: EntryRunner;
    private readonly root: HTMLDivElement;

    public constructor(runner?: EntryRunner) {
        super();

        const shadow = this.attachShadow({mode: 'open'});

        this.runner = runner ?? createEntryRunner();

        this.loadStyles(shadow);

        this.root = document.createElement('div');
        shadow.appendChild(this.root);
    }

    public static get observedAttributes(): string[] {
        const attribute = EntryElement.entryIdAttribute;

        const attributes = [attribute];

        return attributes;
    }

    public connectedCallback(): void {
        this.renderLoading();

        this.run();
    }

    public attributeChangedCallback(
        name: string,
        oldValue: string | null,
        newValue: string | null
    ): void {
        const watched = EntryElement.entryIdAttribute;

        if (name !== watched) {
            return;
        }

        if (oldValue === newValue) {
            return;
        }

        if (!this.isConnected) {
            return;
        }

        this.renderLoading();

        this.run();
    }

    private async run(): Promise<void> {
        try {
            const entryId = this.getEntryId();

            if (!entryId) {
                const message = 'Entry id is missing.';
                this.renderError(message);

                return;
            }

            const response = await this.runner.run(entryId);

            if (response.success) {
                const data = response.data;

                if (!data) {
                    const message = 'Failed to load entry. Please try again.';
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

    private getEntryId(): string {
        const attribute = EntryElement.entryIdAttribute;

        const raw = this.getAttribute(attribute);

        if (!raw) {
            const empty = '';
            return empty;
        }

        const trimmed = raw.trim();

        return trimmed;
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

        const message = 'Failed to load entry. Please try again.';
        return message;
    }

    private pickErrorMessage(response: UseCaseResponse<GetEntryResponse>): string {
        const errors = response.errors;

        if (errors && errors.length) {
            const message = errors.join(', ');
            return message;
        }

        const fallback = 'Failed to load entry. Please try again.';

        return fallback;
    }

    private renderLoading(): void {
        const html = `
            <div class="dl-entry container" data-testid="loading">
                <div class="box">
                    <progress class="progress is-small is-primary" max="100">Loading</progress>
                    <p class="has-text-grey">Loading entry&hellip;</p>
                </div>
            </div>
        `;

        this.root.innerHTML = html;
    }

    private renderError(message: string): void {
        const html = `
            <div class="dl-entry container" data-testid="error">
                <article class="message is-danger is-light">
                    <div class="message-body">
                        ${message}
                    </div>
                </article>
            </div>
        `;

        this.root.innerHTML = html;
    }

    private renderData(entry: GetEntryResponse): void {
        const titleText = entry.title;
        const dateText = entry.date;
        const bodyText = entry.body;

        const html = `
            <div class="dl-entry container" data-testid="data">
                <div class="card">
                    <header class="card-header">
                        <p class="card-header-title">
                            ${titleText}
                        </p>
                    </header>

                    <div class="card-content">
                        <div class="content">
                            <p class="has-text-grey">
                                ${dateText}
                            </p>

                            <hr />

                            <p>
                                ${bodyText}
                            </p>
                        </div>
                    </div>

                    <footer class="card-footer">
                        <a class="card-footer-item" href="/entries">Back to list</a>
                    </footer>
                </div>
            </div>
        `;

        this.root.innerHTML = html;
    }
}
