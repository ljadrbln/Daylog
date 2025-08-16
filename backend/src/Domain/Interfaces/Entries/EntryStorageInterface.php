<?php
declare(strict_types=1);

namespace Daylog\Domain\Interfaces\Entries;

use Daylog\Domain\Models\Entries\Entry;

/**
 * Interface EntryStorageInterface
 *
 * Defines a low-level persistence contract for Entry.
 * Implementations are database-specific.
 */
interface EntryStorageInterface
{
    /**
     * Insert the given Entry and return generated UUID.
     *
     * @param Entry $entry Entry to be persisted.
     * @return string Generated UUID for the new record.
     */
    public function insert(Entry $entry): string;
}
