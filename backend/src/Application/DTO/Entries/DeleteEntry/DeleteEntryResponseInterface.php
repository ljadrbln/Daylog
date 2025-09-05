<?php
declare(strict_types=1);

namespace Daylog\Application\DTO\Entries\DeleteEntry;

/**
 * Response DTO contract for UC-4 DeleteEntry.
 *
 * Purpose:
 * Return a lightweight confirmation that includes the deleted entry UUID.
 *
 * Mechanics:
 * - Use cases construct this object on successful deletion.
 */
interface DeleteEntryResponseInterface
{
    /**
     * Get deleted entry UUID.
     *
     * @return string UUID string that was deleted.
     */
    public function getId(): string;
}
