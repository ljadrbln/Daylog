<?php
declare(strict_types=1);

namespace Daylog\Application\DTO\Entries\AddEntry;

use Daylog\Domain\Models\Entries\Entry;

/**
 * Response DTO for UC-1 Add Entry.
 *
 * Purpose:
 * Encapsulates the newly created Entry state immediately after persistence.
 *
 * Mechanics:
 * - Constructed from a domain Entry using the factory method fromEntry().
 * - Provides getEntry() to access the domain snapshot.
 * - DTO is immutable after construction.
 */
final class AddEntryResponse implements AddEntryResponseInterface
{
    private Entry $entry;

    /**
     * @param Entry $entry Newly created and already persisted domain entity.
     */
    private function __construct(Entry $entry)
    {
        $this->entry = $entry;
    }

    /**
     * Factory method to create a response from a domain Entry.
     *
     * @param Entry $entry Domain model representing the persisted entry.
     * @return self
     */
    public static function fromEntry(Entry $entry): self
    {
        $response = new self($entry);
        return $response;
    }

    /**
     * Get the encapsulated domain Entry.
     *
     * @return Entry Snapshot of the newly created entry.
     */
    public function getEntry(): Entry
    {
        return $this->entry;
    }
}
