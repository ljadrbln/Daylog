/**
 * Vanilla Web Component for UC-1 (AddEntry).
 *
 * Purpose:
 * - Execute UC-1 via UseCaseRunner + AddEntry provider (through AddEntryRunner).
 * - Render form + UI states inside shadow DOM: idle, submitting, error, success.
 *
 * Mechanics:
 * - A shared stylesheet <link> (/assets/css/dl-components.css) is appended once
 *   and kept in shadow root.
 * - All render methods update only a dedicated root container, so the stylesheet
 *   is never removed during re-rendering.
 * - On successful creation, the component dispatches a `dl-entry-created` event
 *   with `{ id }` and optionally redirects using `redirect-to="/entries/{id}"`.
 *
 * Notes:
 * - The component depends on runner/provider wiring only.
 * - It does not access HTTP or repositories directly.
 */

import type {Entry} from '@src/Domain/Models/Entries/Entry';
import type {AddEntryResponse} from '@src/Application/DTO/Entries/AddEntry/AddEntryResponse';
import type {AddEntryRunner} from './AddEntryRunner';
import {createAddEntryRunner} from './AddEntryRunner';

export class AddEntryElement extends HTMLElement {
    private readonly runner: AddEntryRunner;
    private readonly root: HTMLDivElement;

    private isSubmitting = false;

    public constructor(runner?: AddEntryRunner) {
        super();

        const shadow = this.attachShadow({mode: 'open'});

        this.runner = runner ?? createAddEntryRunner();

        this.loadStyles(shadow);

        this.root = document.createElement('div');
        shadow.appendChild(this.root);
    }

    public connectedCallback(): void {
        this.renderForm();
        this.bindEvents();
    }

    private loadStyles(shadow: ShadowRoot): void {
        const href = '/assets/css/dl-components.css';

        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = href;

        shadow.appendChild(link);
    }

    private bindEvents(): void {
        const form = this.root.querySelector('form[data-testid="form"]');

        if (!form) {
            return;
        }

        form.addEventListener('submit', (event: Event) => {
            event.preventDefault();

            if (this.isSubmitting) {
                return;
            }

            this.submit();
        });

        const cancel = this.root.querySelector('button[data-testid="cancel"]');

        if (cancel) {
            cancel.addEventListener('click', () => {
                this.onCancel();
            });
        }
    }

    private async submit(): Promise<void> {
        this.isSubmitting = true;

        const params = this.readFormParams();

        this.renderSubmitting(params);
        this.bindEvents();

        try {
            const response = await this.runner.run(params);

            if (response.success) {
                const entry = response.data;

                if (!entry) {
                    const message = 'Failed to create entry. Please try again.';
                    this.isSubmitting = false;

                    this.renderError(message, params);
                    this.bindEvents();

                    return;
                }

                this.isSubmitting = false;

                this.renderSuccess(entry);
                this.afterSuccess(entry);

                return;
            }

            const message = this.pickErrorMessage(response);

            this.isSubmitting = false;

            this.renderError(message, params);
            this.bindEvents();
        } catch (error) {
            const message = 'Failed to create entry. Please try again.';

            this.isSubmitting = false;

            this.renderError(message, params);
            this.bindEvents();
        }
    }

    private readFormParams(): {title: string; body: string; date: string} {
        const title = this.readInputValue('title');
        const body = this.readInputValue('body');
        const date = this.readInputValue('date');

        const params = {title, body, date};

        return params;
    }

    private readInputValue(name: 'title' | 'body' | 'date'): string {
        const selector = `[name="${name}"]`;
        const node = this.root.querySelector(selector);

        if (!node) {
            const empty = '';
            return empty;
        }

        if (node instanceof HTMLInputElement) {
            const value = node.value.trim();
            return value;
        }

        if (node instanceof HTMLTextAreaElement) {
            const value = node.value.trim();
            return value;
        }

        const empty = '';
        return empty;
    }

    private pickErrorMessage(response: AddEntryResponse): string {
        const errors = response.errors;

        if (errors && errors.length) {
            const message = errors.join(', ');
            return message;
        }

        const fallback = 'Failed to create entry. Please try again.';
        return fallback;
    }

    private renderForm(params?: {title: string; body: string; date: string}): void {
        const titleValue = params?.title ?? '';
        const bodyValue = params?.body ?? '';
        const dateValue = params?.date ?? '';

        const html = `
            <div class="dl-entry-add container" data-testid="idle">
                <div class="card">
                    <header class="card-header">
                        <p class="card-header-title">
                            New entry
                        </p>
                    </header>

                    <div class="card-content">
                        <div class="content">
                            <form data-testid="form">
                                <div class="field">
                                    <label class="label">Title</label>
                                    <div class="control">
                                        <input
                                            class="input"
                                            type="text"
                                            name="title"
                                            value="${this.escapeAttr(titleValue)}"
                                            autocomplete="off"
                                            required
                                        />
                                    </div>
                                </div>

                                <div class="field">
                                    <label class="label">Date</label>
                                    <div class="control">
                                        <input
                                            class="input"
                                            type="date"
                                            name="date"
                                            value="${this.escapeAttr(dateValue)}"
                                            placeholder="YYYY-MM-DD"
                                            autocomplete="off"
                                            required
                                        />
                                    </div>
                                    <p class="help">Logical entry date, not timestamps.</p>
                                </div>

                                <div class="field">
                                    <label class="label">Body</label>
                                    <div class="control">
                                        <textarea
                                            class="textarea"
                                            name="body"
                                            rows="8"
                                            required
                                        >${this.escapeHtml(bodyValue)}</textarea>
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="control">
                                        <div class="buttons">
                                            <button class="button is-primary" type="submit">
                                                Save
                                            </button>

                                            <button
                                                class="button"
                                                type="button"
                                                data-testid="cancel"
                                            >
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        `;

        this.root.innerHTML = html;
    }

    private renderSubmitting(params: {title: string; body: string; date: string}): void {
        const titleValue = params.title;
        const bodyValue = params.body;
        const dateValue = params.date;

        const html = `
            <div class="dl-entry-add container" data-testid="submitting">
                <div class="card">
                    <header class="card-header">
                        <p class="card-header-title">
                            New entry
                        </p>
                    </header>

                    <div class="card-content">
                        <div class="content">
                            <progress class="progress is-small is-primary" max="100">Loading</progress>
                            <p class="has-text-grey">Saving entry&hellip;</p>

                            <hr />

                            <form data-testid="form">
                                <div class="field">
                                    <label class="label">Title</label>
                                    <div class="control">
                                        <input
                                            class="input"
                                            type="text"
                                            name="title"
                                            value="${this.escapeAttr(titleValue)}"
                                            disabled
                                        />
                                    </div>
                                </div>

                                <div class="field">
                                    <label class="label">Date</label>
                                    <div class="control">
                                        <input
                                            class="input"
                                            type="text"
                                            name="date"
                                            value="${this.escapeAttr(dateValue)}"
                                            disabled
                                        />
                                    </div>
                                </div>

                                <div class="field">
                                    <label class="label">Body</label>
                                    <div class="control">
                                        <textarea
                                            class="textarea"
                                            name="body"
                                            rows="8"
                                            disabled
                                        >${this.escapeHtml(bodyValue)}</textarea>
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="control">
                                        <div class="buttons">
                                            <button class="button is-primary" type="button" disabled>
                                                Save
                                            </button>

                                            <button class="button" type="button" data-testid="cancel" disabled>
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        `;

        this.root.innerHTML = html;
    }

    private renderError(
        message: string,
        params: {title: string; body: string; date: string}
    ): void {
        const html = `
            <div class="dl-entry-add container" data-testid="error">
                <article class="message is-danger is-light">
                    <div class="message-body">
                        ${this.escapeHtml(message)}
                    </div>
                </article>

                <div class="mt-4">
                    ${this.renderFormHtml(params)}
                </div>
            </div>
        `;

        this.root.innerHTML = html;
    }

    private renderSuccess(entry: Entry): void {
        const idText = (entry as unknown as {id?: string}).id ?? '';

        const html = `
            <div class="dl-entry-add container" data-testid="success">
                <article class="message is-success is-light">
                    <div class="message-body">
                        Entry created successfully.
                    </div>
                </article>

                <div class="card mt-4">
                    <header class="card-header">
                        <p class="card-header-title">
                            ${this.escapeHtml(entry.title)}
                        </p>
                    </header>

                    <div class="card-content">
                        <div class="content">
                            <p class="has-text-grey">
                                ${this.escapeHtml(entry.date)}
                            </p>

                            <hr />

                            <p>
                                ${this.escapeHtml(entry.body)}
                            </p>

                            <div class="tags mt-4">
                                <span class="tag is-light">id</span>
                                <span class="tag is-info is-light">${this.escapeHtml(idText)}</span>
                            </div>
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

    private renderFormHtml(params: {title: string; body: string; date: string}): string {
        const titleValue = params.title;
        const bodyValue = params.body;
        const dateValue = params.date;

        const html = `
            <div class="card">
                <header class="card-header">
                    <p class="card-header-title">
                        New entry
                    </p>
                </header>

                <div class="card-content">
                    <div class="content">
                        <form data-testid="form">
                            <div class="field">
                                <label class="label">Title</label>
                                <div class="control">
                                    <input
                                        class="input"
                                        type="text"
                                        name="title"
                                        value="${this.escapeAttr(titleValue)}"
                                        autocomplete="off"
                                    />
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">Date</label>
                                <div class="control">
                                    <input
                                        class="input"
                                        type="text"
                                        name="date"
                                        value="${this.escapeAttr(dateValue)}"
                                        placeholder="YYYY-MM-DD"
                                        autocomplete="off"
                                    />
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">Body</label>
                                <div class="control">
                                    <textarea
                                        class="textarea"
                                        name="body"
                                        rows="8"
                                    >${this.escapeHtml(bodyValue)}</textarea>
                                </div>
                            </div>

                            <div class="field">
                                <div class="control">
                                    <div class="buttons">
                                        <button class="button is-primary" type="submit">
                                            Save
                                        </button>

                                        <button
                                            class="button"
                                            type="button"
                                            data-testid="cancel"
                                        >
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        `;

        return html;
    }

    private afterSuccess(entry: Entry): void {
        const idText = (entry as unknown as {id?: string}).id ?? '';

        const event = new CustomEvent('dl-entry-created', {
            detail: {id: idText},
            bubbles: true,
            composed: true
        });

        this.dispatchEvent(event);
    }

    private onCancel(): void {
        window.location.href = '/entries';
    }

    private escapeHtml(value: string): string {
        const text = value ?? '';

        const escaped = text
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');

        return escaped;
    }

    private escapeAttr(value: string): string {
        const escaped = this.escapeHtml(value);
        return escaped;
    }
}
