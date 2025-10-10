/**
 * UC-4: Delete Entry — request DTO.
 *
 * Describes the path parameter required by DELETE /api/entries/:id.
 * The gateway will serialize this DTO by substituting {id} into the URL.
 */
export type DeleteEntryRequest = {
    /**
     * Entry identifier (UUID v4 as produced by backend).
     * Validation of format/semantics is out of scope for the gateway DTO.
     */
    id: string;
};
