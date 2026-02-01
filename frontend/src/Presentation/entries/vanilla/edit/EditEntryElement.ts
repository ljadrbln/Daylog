/**
 * Vanilla Web Component for UC-5 (UpdateEntry), using UC-3 (GetEntry) for prefill.
 *
 * Purpose:
 * - Load an entry by id (UC-3) and prefill the shared entry form UI.
 * - Submit updated values (UC-5) via UseCaseRunner + UpdateEntry provider.
 * - Render UI states inside shadow DOM: loading, idle, submitting, error, success.
 *
 * Mechanics:
 * - A shared stylesheet <link> (/assets/css/dl-components.css) is appended once
 *   and kept in shadow root.
 * - The component keeps a dedicated root container and re-renders within it.
 * - Entry id is read from the `entry-id` attribute. On changes, the component reloads data.
 * - Form UI is delegated to EntryFormView to avoid duplication with AddEntry.
 *
 * Notes:
 * - The component depends on runner/provider wiring only.
 * - It does not access HTTP or repositories directly.
 * - The component does not redirect or navigate on success; it emits an event.
 */

import type {UseCaseResponse} from '@src/Application/DTO/Common/UseCaseResponse';
import type {GetEntryResponse} from '@src/Application/DTO/Entries/GetEntry/GetEntryResponse';
import type {Entry} from '@src/Domain/Models/Entries/Entry';
import {EntryFormView, type EntryFormParams} from '@src/Presentation/entries/vanilla/form/EntryFormView';
import type {EntryRunner} from '@src/Presentation/entries/vanilla/view/EntryRunner';
import {createEntryRunner} from '@src/Presentation/entries/vanilla/view/EntryRunner';
import type {UpdateEntryRunner} from './UpdateEntryRunner';
import {createUpdateEntryRunner} from './UpdateEntryRunner';

type UpdateParams = {
    id: string;
    title: string;
    body: string;
    date: string;
};

export class EditEntryElement extends HTMLElement {
    private static readonly entryIdAttribute = 'entry-id';

    private readonly getRunner: EntryRunner;
    private readonly updateRunner: UpdateEntryRunner;
    private readonly root: HTMLDivElement;
    private readonly formView: EntryFormView;

    private isSubmitting = false;

    public constructor(getRunner?: EntryRunner, updateRunner?: UpdateEntryRunner) {
        super();

        const shadow = this.attachShadow({mode: 'open'});

        this.getRunner = getRunner ?? createEntryRunner();
        this.updateRunner = updateRunner ?? createUpdateEntryRunner();

        this.loadStyles(shadow);

        this.root = document.createElement('div');
        shadow.appendChild(this.root);

        this.formView = new EntryFormView({
            containerClass: 'dl-entry-edit',
            labels: {
                heading: 'Edit entry',
                submit: 'Save',
                cancel: 'Cancel',
                dateHelp: 'Logical entry date, not timestamps.',
                submittingText: 'Saving changes…'
            }
        });
    }

    public static get observedAttributes(): string[] {
        return [];
    }

    public connectedCallback(): void {
        this.renderLoading();
        this.load();
    }

    private async load(): Promise<void> {
        try {
            const entryId = this.getEntryId();

            if (!entryId) {
                const message = 'Entry id is missing.';
                this.renderError(message, this.emptyParams());
                this.bindEvents();

                return;
            }

            const response = await this.getRunner.run(entryId);

            if (response.success) {
                const data = response.data;

                if (!data) {
                    const message = 'Failed to load entry. Please try again.';
                    this.renderError(message, this.emptyParams());
                    this.bindEvents();

                    return;
                }

                const params = this.mapEntryToParams(data);

                this.renderForm(params);
                this.bindEvents();

                return;
            }

            const message = this.pickGetErrorMessage(response);

            this.renderError(message, this.emptyParams());
            this.bindEvents();
        } catch (error) {
            const message = 'Failed to load entry. Please try again.';

            this.renderError(message, this.emptyParams());
            this.bindEvents();
        }
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

        const entryId = this.getEntryId();

        const updateParams: UpdateParams = {
            id: entryId,
            title: params.title,
            body: params.body,
            date: params.date
        };

        this.renderSubmitting(params);
        this.bindEvents();

        try {
            const response = await this.updateRunner.run(updateParams);

            if (response.success) {
                const data = response.data;

                if (!data) {
                    const message = 'Failed to update entry. Please try again.';
                    this.isSubmitting = false;

                    this.renderError(message, params);
                    this.bindEvents();

                    return;
                }

                this.isSubmitting = false;

                const updatedId = this.pickUpdatedId(entryId, data);

                this.renderSuccess();
                this.afterSuccess(updatedId);

                return;
            }

            const message = this.pickUpdateErrorMessage(response);

            this.isSubmitting = false;

            this.renderError(message, params);
            this.bindEvents();
        } catch (error) {
            const message = 'Failed to update entry. Please try again.';

            this.isSubmitting = false;

            this.renderError(message, params);
            this.bindEvents();
        }
    }

    private pickUpdatedId(entryId: string, data: unknown): string {
        const candidate = data as {id?: string} | null;

        const id = candidate?.id;

        if (id && typeof id === 'string') {
            const trimmed = id.trim();
            return trimmed;
        }

        return entryId;
    }

    private pickGetErrorMessage(response: UseCaseResponse<GetEntryResponse>): string {
        const errors = response.errors;

        if (errors && errors.length) {
            const message = errors.join(', ');
            return message;
        }

        const fallback = 'Failed to load entry. Please try again.';
        return fallback;
    }

    private pickUpdateErrorMessage(response: UseCaseResponse<Entry>): string {
        const errors = response.errors;

        if (errors && errors.length) {
            const message = errors.join(', ');
            return message;
        }

        const fallback = 'Failed to update entry. Please try again.';
        return fallback;
    }

    private mapEntryToParams(entry: GetEntryResponse): EntryFormParams {
        const params: EntryFormParams = {
            title: entry.title ?? '',
            body: entry.body ?? '',
            date: entry.date ?? ''
        };

        return params;
    }

    private renderLoading(): void {
        const html = `
            <div class="dl-entry-edit container" data-testid="loading">
                <div class="box">
                    <progress class="progress is-small is-primary" max="100">Loading</progress>
                    <p class="has-text-grey">Loading entry&hellip;</p>
                </div>
            </div>
        `;

        this.root.innerHTML = html;
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

    private renderSuccess(): void {
        const html = `
            <div class="dl-entry-edit container" data-testid="success">
                <article class="message is-success is-light">
                    <div class="message-body">
                        Entry updated successfully.
                    </div>
                </article>

                <div class="mt-4">
                    <a class="button is-light" href="/entries">Back to list</a>
                </div>
            </div>
        `;

        this.root.innerHTML = html;
    }

    private afterSuccess(entryId: string): void {
        const event = new CustomEvent('dl-entry-updated', {
            detail: {id: entryId},
            bubbles: true,
            composed: true
        });

        this.dispatchEvent(event);
    }

    private onCancel(): void {
        window.location.href = '/entries';
    }

    private getEntryId(): string {
        const attribute = EditEntryElement.entryIdAttribute;

        const raw = this.getAttribute(attribute);

        if (!raw) {
            const empty = '';
            return empty;
        }

        const trimmed = raw.trim();

        return trimmed;
    }

    private emptyParams(): EntryFormParams {
        const params: EntryFormParams = {title: '', body: '', date: ''};

        return params;
    }

    private loadStyles(shadow: ShadowRoot): void {
        const href = '/assets/css/dl-components.css';

        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = href;

        shadow.appendChild(link);
    }
}
