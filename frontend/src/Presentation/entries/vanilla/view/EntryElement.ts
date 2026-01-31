/**
 * Vanilla Web Component for UC-3 (GetEntry).
 *
 * Purpose:
 * - Execute UC-3 via UseCaseRunner + GetEntry provider.
 * - Render three UI states inside shadow DOM: loading, error, data.
 * - Provide entry actions in the view UI: edit and delete (UC-4).
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
import type {DeleteEntryRunner} from './DeleteEntryRunner';
import {createDeleteEntryRunner} from './DeleteEntryRunner';

export class EntryElement extends HTMLElement {
    private static readonly entryIdAttribute = 'entry-id';

    private readonly runner: EntryRunner;
    private readonly deleteRunner: DeleteEntryRunner;
    private readonly root: HTMLDivElement;

    public constructor(runner?: EntryRunner, deleteRunner?: DeleteEntryRunner) {
        super();

        const shadow = this.attachShadow({mode: 'open'});

        this.runner = runner ?? createEntryRunner();
        this.deleteRunner = deleteRunner ?? createDeleteEntryRunner();

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
                this.bindActions();

                return;
            }

            const message = this.pickErrorMessage(response);

            this.renderError(message);
        } catch (error) {
            const message = this.normalizeError(error);

            this.renderError(message);
        }
    }

    private bindActions(): void {
        const deleteButton = this.root.querySelector('[data-testid="delete"]');

        if (deleteButton) {
            deleteButton.addEventListener('click', (event) => {
                event.preventDefault();
                this.handleDelete();
            });
        }
    }

    private async handleDelete(): Promise<void> {
        const entryId = this.getEntryId();

        if (!entryId) {
            const message = 'Entry id is missing.';
            this.renderError(message);

            return;
        }

        const confirmed = window.confirm('Delete this entry?');

        if (!confirmed) {
            return;
        }

        this.renderDeleting();

        try {
            const response = await this.deleteRunner.run(entryId);

            if (response.success) {
                const event = new CustomEvent('dl-entry-deleted', {
                    detail: {id: entryId},
                    bubbles: true,
                    composed: true
                });

                this.dispatchEvent(event);

                this.renderDeleted();

                return;
            }

            const message = this.pickDeleteErrorMessage(response);

            this.renderError(message);
        } catch (error) {
            const message = 'Failed to delete entry. Please try again.';
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

    private pickDeleteErrorMessage(response: UseCaseResponse<null>): string {
        const errors = response.errors;

        if (errors && errors.length) {
            const message = errors.join(', ');
            return message;
        }

        const fallback = 'Failed to delete entry. Please try again.';

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

    private renderDeleting(): void {
        const html = `
            <div class="dl-entry container" data-testid="deleting">
                <div class="box">
                    <progress class="progress is-small is-danger" max="100">Deleting</progress>
                    <p class="has-text-grey">Deleting entry&hellip;</p>
                </div>
            </div>
        `;

        this.root.innerHTML = html;
    }

    private renderDeleted(): void {
        const html = `
            <div class="dl-entry container" data-testid="deleted">
                <article class="message is-success is-light">
                    <div class="message-body">
                        Entry deleted.
                    </div>
                </article>

                <div class="buttons mt-4">
                    <a class="button is-light" href="/entries">
                        Back
                    </a>
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
        const entryId = this.getEntryId();

        const titleText = entry.date;
        const createdAtText = entry.createdAt ?? '';
        const updatedAtText = entry.updatedAt ?? '';
        const bodyText = entry.body;

        const editHref = `/entries/${entryId}/edit`;

        const html = `
            <section class="section" data-testid="data">
                <div class="container">
                    <div class="box">
                        <h1 class="title is-3">
                            ${titleText}
                        </h1>

                        <p class="subtitle is-6 has-text-grey">
                            Created: ${createdAtText} · Updated: ${updatedAtText}
                        </p>

                        <div class="content">
                            <p>${bodyText}</p>
                        </div>

                        <div class="mt-5 is-flex is-align-items-center is-justify-content-space-between is-fullwidth">
                            <div class="is-flex">
                                <a
                                    href="${editHref}"
                                    class="button is-link is-light mr-2"
                                    data-testid="edit"
                                >
                                    Edit
                                </a>

                                <button
                                    type="button"
                                    class="button is-danger is-light"
                                    data-testid="delete"
                                >
                                    Delete
                                </button>
                            </div>

                            <a href="/entries" class="button is-light" data-testid="back">
                                Back
                            </a>
                        </div>
                    </div>
                </div>
            </section>
        `;

        this.root.innerHTML = html;
    }
}
