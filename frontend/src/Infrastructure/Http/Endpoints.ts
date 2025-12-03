/**
 * Centralized API endpoint definitions for all Entry-related operations.
 *
 * Purpose:
 * Provide a single source of truth for REST endpoint paths used across
 * the Infrastructure layer. This ensures consistency between repositories,
 * validators, and test factories.
 *
 * Mechanics:
 * - `entries()` — returns the base path for collection-level operations (UC-1, UC-2, UC-5).
 * - `entryById(id)` — returns the full path for single-entry operations (UC-3, UC-4).
 *
 * Usage examples:
 * ```ts
 * const url = endpoints.entries();           // "/api/entries"
 * const byId = endpoints.entryById(entryId); // "/api/entries/123"
 * ```
 *
 * @module Infrastructure/Http/endpoints
 * @returns {object} Object with helper functions for endpoint path generation.
 */

export const Endpoints = {
    entries: () => '/entries',
    entryById: (id: string) => `/entries/${id}`
};
