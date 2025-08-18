<?php

declare(strict_types=1);

namespace Daylog\Domain\Interfaces\Entries;

use Daylog\Domain\Models\Entries\Entry;

/**
 * Repository contract for Entry persistence.
 */
interface EntryRepositoryInterface
{
    /**
     * Save a new Entry into storage.
     *
     * @param Entry $entry
     * @return array<string,string> Result with keys:
     *                             - id (UUID v4)
     *                             - title (string)
     *                             - body (string)
     *                             - date (YYYY-MM-DD)
     *                             - createdAt (ISO-8601 UTC)
     *                             - updatedAt (ISO-8601 UTC)
     */
    public function save(Entry $entry): array;
}
