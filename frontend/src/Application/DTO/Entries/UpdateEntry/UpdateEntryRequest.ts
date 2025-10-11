/**
 * UC-5: Update Entry — request DTO.
 *
 * Represents the transport-level payload to update an existing entry.
 * Gateway enforces only transport shape; business rules are validated elsewhere.
 */
export type UpdateEntryRequest = {
    id: string;
    title?: string;
    body?: string;
    date?: string; // strict YYYY-MM-DD
};
