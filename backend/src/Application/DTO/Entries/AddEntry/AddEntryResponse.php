<?php
declare(strict_types=1);

namespace Daylog\Application\DTO\Entries\AddEntry;

use Daylog\Domain\Models\Entries\Entry;
use Daylog\Application\Transformers\Entries\EntryTransformer;

/**
 * Response DTO for UC-1 Add Entry.
 *
 * Purpose:
 * Encapsulates the newly created Entry state immediately after persistence.
 *
 * Mechanics:
 * - Constructed from a domain Entry using the factory method fromEntry().
 * - Provides getEntry() for internal access if needed by Application.
 * - Implements UseCaseResponseInterface so Presentation can safely consume
 *   a flat associative payload via toArray().
 *
 * @implements UseCaseResponseInterface<array{
 *   id: string,
 *   title: string,
 *   body: string,
 *   date: string,
 *   createdAt: string,
 *   updatedAt: string
 * }>
 */
final class AddEntryResponse implements AddEntryResponseInterface
{
    /**
     * @param Entry $entry Newly created and already persisted domain entity.
     */
    private function __construct(
        private Entry $entry
    ) {}    

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

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        $entry   = $this->entry;
        $payload = EntryTransformer::fromEntry($entry);

        return $payload;
    }
}