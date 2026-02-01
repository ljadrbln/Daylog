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
 *   with `{ id }`.
 *
 * Notes:
 * - The component depends on runner/provider wiring only.
 * - It does not access HTTP or repositories directly.
 */

import type {Entry} from '@src/Domain/Models/Entries/Entry';
import type {AddEntryResponse} from '@src/Application/DTO/Entries/AddEntry/AddEntryResponse';
import type {AddEntryRunner} from './AddEntryRunner';
import {createAddEntryRunner} from './AddEntryRunner';
import {EntryFormView, type EntryFormParams} from '@src/Presentation/entries/vanilla/form/EntryFormView';

export class AddEntryElement extends HTMLElement {
    private readonly runner: AddEntryRunner;
    private readonly root: HTMLDivElement;
    private readonly formView: EntryFormView;

    private isSubmitting = false;

    public constructor(runner?: AddEntryRunner) {
        super();

        const shadow = this.attachShadow({mode: 'open'});

        this.runner = runner ?? createAddEntryRunner();

        this.loadStyles(shadow);

        this.root = document.createElement('div');
        shadow.appendChild(this.root);

        this.formView = new EntryFormView({
            containerClass: 'dl-entry-add',
            labels: {
                heading: 'New entry',
                submit: 'Save',
                cancel: 'Cancel',
                dateHelp: 'Logical entry date, not timestamps.',
                submittingText: 'Saving entry…'
            }
        });
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
        this.formView.bind(this.root, {
            onSubmit: (params: EntryFormParams) => {
                if (this.isSubmitting) {
                    return;
                }

                this.submit(params);
            },
            onCancel: () => {
                this.onCancel();
            }
        });
    }

    private async submit(params: EntryFormParams): Promise<void> {
        this.isSubmitting = true;

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

    private pickErrorMessage(response: AddEntryResponse): string {
        const errors = response.errors;

        if (errors && errors.length) {
            const message = errors.join(', ');
            return message;
        }

        const fallback = 'Failed to create entry. Please try again.';
        return fallback;
    }

    private renderForm(params?: EntryFormParams): void {
        this.formView.renderIdle(this.root, params);
    }

    private renderSubmitting(params: EntryFormParams): void {
        this.formView.renderSubmitting(this.root, params);
    }

    private renderError(message: string, params: EntryFormParams): void {
        this.formView.renderError(this.root, message, params);
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
}
