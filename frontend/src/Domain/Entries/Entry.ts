/**
 * Domain model for a single journal entry used across the app.
 *
 * Minimal shape the UI/Gateway rely on. Business-level validation of
 * field contents happens in higher layers when UI starts using them.
 *
 * ISO-8601 (YYYY-MM-DD) is used for event date and ISO-8601 datetime with offset is used for timestamps.
 */
export interface Entry {
    id: string;
    title: string;
    body: string;
    date: string;
    createdAt: string;
    updatedAt: string;
}
