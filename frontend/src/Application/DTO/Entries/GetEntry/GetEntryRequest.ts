/**
 * UC-3: Get Entry — request DTO.
 *
 * Describes the path parameter required by /api/entries/:id.
 * The gateway will serialize this DTO by substituting {id} into the URL.
 */
export type GetEntryRequest = {
    /**
     * Entry identifier (UUID v4 as produced by backend).
     * Validation of format/semantics is out of scope for the gateway DTO.
     */
    id: string;
};
