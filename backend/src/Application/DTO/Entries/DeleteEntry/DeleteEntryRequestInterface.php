<?php
declare(strict_types=1);

namespace Daylog\Application\DTO\Entries\DeleteEntry;

/**
 * Request DTO contract for UC-4 DeleteEntry.
 *
 * Purpose:
 * Represent a validated transport payload for deleting an entry by UUID.
 *
 * Mechanics:
 * Implementations must store the UUID and expose it via getId().
 */
interface DeleteEntryRequestInterface
{
    /**
     * Get target entry UUID.
     *
     * @return string Non-empty UUID string.
     */
    public function getId(): string;
}
