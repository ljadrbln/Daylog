<?php
declare(strict_types=1);

namespace Daylog\Application\DTO\Entries\UpdateEntry;

use Daylog\Domain\Models\Entries\Entry;
use Daylog\Application\Transformers\Entries\EntryTransformer;

/**
 * Response DTO for UC-5 Update Entry.
 *
 * Purpose:
 * Encapsulates the updated Entry state immediately after persistence.
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
final class UpdateEntryResponse implements UpdateEntryResponseInterface
{
    /**
     * @param Entry $entry Updated and already persisted domain entity.
     */
    private function __construct(
        private Entry $entry
    ) {}

    /**
     * Factory method to create a response from a domain Entry.
     *
     * @param Entry $entry Domain model representing the persisted entry after update.
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
     * @return Entry Snapshot of the updated entry.
     */
    public function getEntry(): Entry
    {
        $entry = $this->entry;
        return $entry;
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
