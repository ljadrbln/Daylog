<?php
declare(strict_types=1);

namespace Daylog\Application\DTO\Entries\AddEntry;

use Daylog\Application\DTO\Common\UseCaseResponseInterface;
use Daylog\Domain\Models\Entries\Entry;

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
final class AddEntryResponse implements AddEntryResponseInterface, UseCaseResponseInterface
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
     * Convert response to a flat associative payload.
     *
     * @return array{
     *   id: string,
     *   title: string,
     *   body: string,
     *   date: string,
     *   createdAt: string,
     *   updatedAt: string
     * }
     */
    public function toArray(): array
    {
        $payload = [
            'id'        => $this->entry->getId(),
            'title'     => $this->entry->getTitle(),
            'body'      => $this->entry->getBody(),
            'date'      => $this->entry->getDate(),
            'createdAt' => $this->entry->getCreatedAt(),
            'updatedAt' => $this->entry->getUpdatedAt(),
        ];
        return $payload;
    }
}