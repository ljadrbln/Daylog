<?php

declare(strict_types=1);

namespace Daylog\Application\DTO\Entries;

/**
 * Interface for AddEntryResponse DTO.
 *
 * Guarantees that every AddEntry use case result
 * provides access to the new entry's identifier and timestamps.
 */
interface AddEntryResponseInterface
{
    /**
     * @return string Entry identifier (UUID v4).
     */
    public function getId(): string;

    /**
     * @return string Logical entry date (YYYY-MM-DD).
     */
    public function getDate(): string;

    /**
     * @return string Creation timestamp (ISO-8601 UTC).
     */
    public function getCreatedAt(): string;

    /**
     * @return string Update timestamp (ISO-8601 UTC).
     */
    public function getUpdatedAt(): string;
}
