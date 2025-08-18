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
     * Increments the save counter, stores the last saved entry,
     * and returns an array with:
     *  - id (string)       — generated UUID v4
     *  - title (string)    — entry title
     *  - body (string)     — entry body
     *  - date (string)     — entry date
     *  - createdAt (string)— ISO-8601 timestamp
     *  - updatedAt (string)— ISO-8601 timestamp
     *
     * @param Entry $entry Entry to save
     * @return array<string,string> Saved entry data
     */
    public function save(Entry $entry): array
    {
        $this->entries[]  = $entry;
        $this->saveCalls++;

        $uuid      = UuidGenerator::generate();
        $timestamp = (new \DateTimeImmutable())->format(DATE_ATOM);

        $result = [
            'id'        => $uuid,
            'title'     => $entry->getTitle(),
            'body'      => $entry->getBody(),
            'date'      => $entry->getDate(),
            'createdAt' => $timestamp,
            'updatedAt' => $timestamp,
        ];
        return $result;
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