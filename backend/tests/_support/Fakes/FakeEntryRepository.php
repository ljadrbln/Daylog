<?php
declare(strict_types=1);

namespace Daylog\Tests\Support\Fakes;

use Daylog\Domain\Interfaces\Entries\EntryRepositoryInterface;
use Daylog\Domain\Models\Entries\Entry;
use Daylog\Domain\Services\UuidGenerator;

/**
 * Fake implementation of EntryRepositoryInterface for tests.
 *
 * Stores entries in memory and allows inspection of save() calls.
 */
final class FakeEntryRepository implements EntryRepositoryInterface
{
    /** @var Entry[] */
    private array $entries = [];

    /** @var int */
    private int $saveCalls = 0;

    /**
     * Saves the given entry to the in-memory storage.
     *
     * @param Entry $entry
     * @return string UUID of the saved entry.
     */
    public function save(Entry $entry): string
    {
        $this->entries[] = $entry;
        $this->saveCalls++;

        $uuid = UuidGenerator::generate();
        return $uuid;
    }

    /**
     * Returns the number of times save() has been called.
     *
     * @return int
     */
    public function getSaveCalls(): int
    {
        return $this->saveCalls;
    }

    /**
     * Returns the last Entry instance passed to save(), or null if none.
     *
     * @return Entry|null
     */
    public function getLastSaved(): ?Entry
    {
        if (empty($this->entries)) {
            return null;
        }

        return end($this->entries);
    }
}