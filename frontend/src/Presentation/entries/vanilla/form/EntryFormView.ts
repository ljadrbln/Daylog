/**
 * EntryFormView is a reusable UI-only helper for entry create/edit forms.
 *
 * Purpose:
 * - Provide a single source of truth for entry form markup (title/date/body + buttons).
 * - Provide rendering helpers for common UI states: idle, submitting, error.
 * - Provide form value reading and safe HTML escaping.
 *
 * Mechanics:
 * - This is NOT a custom element. It is a pure view helper.
 * - The caller owns orchestration (use-case execution, state transitions, navigation).
 * - Every render replaces the container innerHTML; event handlers must be re-bound after render.
 *
 * Notes:
 * - The view supports a configurable container class and labels to avoid hardcoding
 *   Add/Edit-specific copy in the shared layer.
 */

export type EntryFormParams = {
    title: string;
    body: string;
    date: string;
};

export type EntryFormLabels = {
    heading: string;
    submit: string;
    cancel: string;
    dateHelp: string;
    submittingText: string;
};

export type EntryFormCallbacks = {
    onSubmit: (params: EntryFormParams) => void;
    onCancel: () => void;
};

export type EntryFormOptions = {
    containerClass: string;
    labels: EntryFormLabels;
};

export class EntryFormView {
    private readonly options: EntryFormOptions;

    public constructor(options?: Partial<EntryFormOptions>) {
        const defaults = this.defaultOptions();

        const containerClass = options?.containerClass ?? defaults.containerClass;

        const labels: EntryFormLabels = {
            heading: options?.labels?.heading ?? defaults.labels.heading,
            submit: options?.labels?.submit ?? defaults.labels.submit,
            cancel: options?.labels?.cancel ?? defaults.labels.cancel,
            dateHelp: options?.labels?.dateHelp ?? defaults.labels.dateHelp,
            submittingText: options?.labels?.submittingText ?? defaults.labels.submittingText
        };

        this.options = {containerClass, labels};
    }

    /**
     * Render the idle state: editable form.
     *
     * @param {HTMLElement} root Root container whose innerHTML is replaced.
     * @param {EntryFormParams=} params Optional initial values to prefill inputs.
     *
     * @returns {void}
     */
    public renderIdle(root: HTMLElement, params?: EntryFormParams): void {
        const values = params ?? this.emptyParams();

        const html = this.renderCardFormHtml('idle', values, {
            disabled: false,
            showProgress: false
        });

        root.innerHTML = html;
    }

    /**
     * Render the submitting state: disabled form + progress.
     *
     * @param {HTMLElement} root Root container whose innerHTML is replaced.
     * @param {EntryFormParams} params Current values to keep visible while submitting.
     *
     * @returns {void}
     */
    public renderSubmitting(root: HTMLElement, params: EntryFormParams): void {
        const html = this.renderCardFormHtml('submitting', params, {
            disabled: true,
            showProgress: true
        });

        root.innerHTML = html;
    }

    /**
     * Render the error state: Bulma message + editable form with previous values.
     *
     * @param {HTMLElement} root Root container whose innerHTML is replaced.
     * @param {string} message Human-readable error message.
     * @param {EntryFormParams} params Previous values to keep in the form.
     *
     * @returns {void}
     */
    public renderError(root: HTMLElement, message: string, params: EntryFormParams): void {
        const containerClass = this.options.containerClass;

        const html = `
            <div class="${containerClass} container" data-testid="error">
                <article class="message is-danger is-light">
                    <div class="message-body">
                        ${this.escapeHtml(message)}
                    </div>
                </article>

                <div class="mt-4">
                    ${this.renderCardFormHtml('idle', params, {
                        disabled: false,
                        showProgress: false,
                        nested: true
                    })}
                </div>
            </div>
        `;

        root.innerHTML = html;
    }

    /**
     * Bind form events to the currently rendered DOM.
     *
     * Contract:
     * - The caller must call bind() after each render, because render() replaces innerHTML.
     *
     * @param {HTMLElement} root Root container used for querySelector lookups.
     * @param {EntryFormCallbacks} callbacks Submit and cancel callbacks.
     *
     * @returns {void}
     */
    public bind(root: HTMLElement, callbacks: EntryFormCallbacks): void {
        const form = root.querySelector('form[data-testid="form"]');

        if (form) {
            form.addEventListener('submit', (event: Event) => {
                event.preventDefault();

                const params = this.readParams(root);
                callbacks.onSubmit(params);
            });
        }

        const cancel = root.querySelector('button[data-testid="cancel"]');

        if (cancel) {
            cancel.addEventListener('click', () => {
                callbacks.onCancel();
            });
        }
    }

    /**
     * Read current form values from inputs/textarea.
     *
     * @param {HTMLElement} root Root container used for querySelector lookups.
     *
     * @returns {EntryFormParams} Trimmed form values. Missing inputs yield empty strings.
     */
    public readParams(root: HTMLElement): EntryFormParams {
        const title = this.readInputValue(root, 'title');
        const body = this.readInputValue(root, 'body');
        const date = this.readInputValue(root, 'date');

        const params: EntryFormParams = {title, body, date};

        return params;
    }

    private readInputValue(root: HTMLElement, name: 'title' | 'body' | 'date'): string {
        const selector = `[name="${name}"]`;
        const node = root.querySelector(selector);

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

    private renderCardFormHtml(
        stateTestId: 'idle' | 'submitting',
        params: EntryFormParams,
        options: {
            disabled: boolean;
            showProgress: boolean;
            nested?: boolean;
        }
    ): string {
        const containerClass = this.options.containerClass;

        const wrapperStart = options.nested
            ? ''
            : `<div class="${containerClass} container" data-testid="${stateTestId}">`;

        const wrapperEnd = options.nested ? '' : '</div>';

        const titleValue = params.title ?? '';
        const bodyValue = params.body ?? '';
        const dateValue = params.date ?? '';

        const heading = this.options.labels.heading;
        const submitText = this.options.labels.submit;
        const cancelText = this.options.labels.cancel;
        const dateHelp = this.options.labels.dateHelp;
        const submittingText = this.options.labels.submittingText;

        const disabledAttr = options.disabled ? 'disabled' : '';
        const requiredAttr = options.disabled ? '' : 'required';

        const progressHtml = options.showProgress
            ? `
                <progress class="progress is-small is-primary" max="100">Loading</progress>
                <p class="has-text-grey">${this.escapeHtml(submittingText)}</p>

                <hr />
            `
            : '';

        const dateInputType = options.disabled ? 'text' : 'date';
        const datePlaceholder = options.disabled ? '' : 'placeholder="YYYY-MM-DD"';
        const dateAutocomplete = options.disabled ? '' : 'autocomplete="off"';

        const html = `
            ${wrapperStart}
                <div class="card">
                    <header class="card-header">
                        <p class="card-header-title">
                            ${this.escapeHtml(heading)}
                        </p>
                    </header>

                    <div class="card-content">
                        <div class="content">
                            ${progressHtml}

                            <form data-testid="form">
                                <div class="field">
                                    <label class="label">Title</label>
                                    <div class="control">
                                        <input
                                            class="input"
                                            type="text"
                                            name="title"
                                            value="${this.escapeAttr(titleValue)}"
                                            ${dateAutocomplete}
                                            ${requiredAttr}
                                            ${disabledAttr}
                                        />
                                    </div>
                                </div>

                                <div class="field">
                                    <label class="label">Date</label>
                                    <div class="control">
                                        <input
                                            class="input"
                                            type="${dateInputType}"
                                            name="date"
                                            value="${this.escapeAttr(dateValue)}"
                                            ${datePlaceholder}
                                            ${dateAutocomplete}
                                            ${requiredAttr}
                                            ${disabledAttr}
                                        />
                                    </div>
                                    <p class="help">${this.escapeHtml(dateHelp)}</p>
                                </div>

                                <div class="field">
                                    <label class="label">Body</label>
                                    <div class="control">
                                        <textarea
                                            class="textarea"
                                            name="body"
                                            rows="8"
                                            ${requiredAttr}
                                            ${disabledAttr}
                                        >${this.escapeHtml(bodyValue)}</textarea>
                                    </div>
                                </div>

                                <div class="field">
                                    <div class="control">
                                        <div class="buttons">
                                            <button
                                                class="button is-primary"
                                                type="${options.disabled ? 'button' : 'submit'}"
                                                ${options.disabled ? 'disabled' : ''}
                                            >
                                                ${this.escapeHtml(submitText)}
                                            </button>

                                            <button
                                                class="button"
                                                type="button"
                                                data-testid="cancel"
                                                ${options.disabled ? 'disabled' : ''}
                                            >
                                                ${this.escapeHtml(cancelText)}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            ${wrapperEnd}
        `;

        return html;
    }

    private emptyParams(): EntryFormParams {
        const params: EntryFormParams = {title: '', body: '', date: ''};

        return params;
    }

    private defaultOptions(): EntryFormOptions {
        const labels: EntryFormLabels = {
            heading: 'Entry',
            submit: 'Save',
            cancel: 'Cancel',
            dateHelp: 'Logical entry date, not timestamps.',
            submittingText: 'Saving entry…'
        };

        const options: EntryFormOptions = {
            containerClass: 'dl-entry-form',
            labels
        };

        return options;
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
