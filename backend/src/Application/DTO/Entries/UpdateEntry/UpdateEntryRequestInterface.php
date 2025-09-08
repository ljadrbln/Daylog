<?php
declare(strict_types=1);

namespace Daylog\Application\DTO\Entries\UpdateEntry;

/**
 * Interface UpdateEntryRequestInterface
 *
 * Data Transfer Object (DTO) contract for updating an existing diary entry.
 *
 * Purpose:
 * - Transport raw user input (already sanitized and type-checked) into the
 *   application layer for UC-4 UpdateEntry.
 * - Ensure consistent contract between request factories (Presentation) and
 *   use case implementation (Application).
 *
 * Mechanics:
 * - 'id' is required and identifies the entry to update.
 * - 'title', 'body', and 'date' are optional; may be null if not provided in request.
 * - Business validation (length limits, valid date, etc.) happens in validators,
 *   not in this DTO.
 */
interface UpdateEntryRequestInterface
{
    /**
     * Get the identifier of the entry to update.
     *
     * @return string Entry id (UUID v4 expected, validated separately).
     */
    public function getId(): string;

    /**
     * Get the new title of the entry, if provided.
     *
     * @return string|null Trimmed title or null if not provided.
     */
    public function getTitle(): ?string;

    /**
     * Get the new body/content of the entry, if provided.
     *
     * @return string|null Trimmed body or null if not provided.
     */
    public function getBody(): ?string;

    /**
     * Get the new logical date of the entry, if provided.
     *
     * @return string|null Date string in format YYYY-MM-DD or null if not provided.
     */
    public function getDate(): ?string;
}
