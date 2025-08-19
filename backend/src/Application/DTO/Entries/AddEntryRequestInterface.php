<?php
declare(strict_types=1);

namespace Daylog\Application\DTO\Entries;

/**
 * Interface AddEntryRequestInterface
 *
 * Data Transfer Object (DTO) contract for creating a new diary entry.
 * 
 * This interface defines the required getters for transporting user input
 * into the application layer when adding a new entry. It ensures a consistent
 * contract between request factories (Presentation layer) and use cases
 * (Application layer).
 */
interface AddEntryRequestInterface
{
    /**
     * Get the title of the entry.
     *
     * @return string Non-empty title string provided by the user.
     */
    public function getTitle(): string;

    /**
     * Get the body/content of the entry.
     *
     * @return string Body text of the entry, may be long.
     */
    public function getBody(): string;

    /**
     * Get the logical date of the entry.
     *
     * @return string Date string in format YYYY-MM-DD.
     */
    public function getDate(): string;
}
