/**
 * UC-1: Add Entry — request DTO.
 *
 * Represents the transport-level payload required to create a new entry.
 * Higher layers validate lengths and calendar date rules.
 */
export type AddEntryRequest = {
    title: string;
    body: string;
    date: string; // strict YYYY-MM-DD
};
