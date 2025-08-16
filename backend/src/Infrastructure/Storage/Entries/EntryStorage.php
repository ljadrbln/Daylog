<?php
declare(strict_types=1);

namespace Daylog\Infrastructure\Storage\Entries;

use Daylog\Domain\Interfaces\Entries\EntryStorageInterface;
use Daylog\Domain\Models\Entries\Entry;
use Daylog\Domain\Services\UuidGenerator;

/**
 * Class EntryStorage
 *
 * Minimal storage implementation for unit tests.
 * Generates a UUID and returns it; real persistence will be added later.
 */
final class EntryStorage implements EntryStorageInterface
{
    /**
     * Insert the given Entry and return generated UUID.
     *
     * @param Entry $entry Entry to persist (ignored in the minimal version).
     * @return string Generated UUID (v4).
     */
    public function insert(Entry $entry): string
    {
        $uuid = UuidGenerator::generate();
        return $uuid;
    }
}
