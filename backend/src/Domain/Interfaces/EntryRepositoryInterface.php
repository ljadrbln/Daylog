<?php
declare(strict_types=1);

namespace Daylog\Domain\Interfaces;

use Daylog\Domain\Models\Entry;

/**
 * Interface EntryRepositoryInterface
 *
 * Defines persistence operations for Entry aggregates.
 * Implementations are Infrastructure-specific; this interface belongs to Domain.
 */
interface EntryRepositoryInterface
{
    /**
     * Persist the given Entry and return its UUID (string).
     *
     * @param Entry $entry Domain Entry to persist.
     * @return string Non-empty UUID of the saved entry.
     */
    public function save(Entry $entry): string;
}

