<?php
declare(strict_types=1);

namespace Daylog\Application\DTO\Entries\GetEntry;

use Daylog\Domain\Models\Entries\Entry;

/**
 * Response DTO contract for UC-3 GetEntry.
 *
 * Purpose:
 * Define the boundary for delivering a single Entry to callers.
 *
 * Mechanics:
 * - Immutable contract with a single getter.
 */
interface GetEntryResponseInterface
{
    /**
     * Get the retrieved Entry domain model.
     *
     * @return Entry
     */
    public function getEntry(): Entry;
}
