<?php
declare(strict_types=1);

namespace Daylog\Application\DTO\Entries\DeleteEntry;

use Daylog\Domain\Models\Entries\Entry;

/**
 * Concrete response DTO for UC-4 Delete Entry.
 *
 * Purpose:
 * Provide a snapshot of the deleted Entry, and a flat payload for Presentation.
 *
 * Mechanics:
 * - Constructed via fromEntry() to avoid partial/invalid states.
 * - getEntry() returns the domain snapshot.
 * - toArray() returns a scalar-only associative payload.
 *
 * @implements DeleteEntryResponseInterface
 */
final class DeleteEntryResponse implements DeleteEntryResponseInterface
{
    private Entry $entry;

    /**
     * @param Entry $entry Deleted domain entity snapshot.
     */
    private function __construct(Entry $entry)
    {
        $this->entry = $entry;
    }

    /**
     * {@inheritDoc}
     */
    public static function fromEntry(Entry $entry): self
    {
        $response = new self($entry);
        return $response;
    }

    /**
     * {@inheritDoc}
     */
    public function getEntry(): Entry
    {
        return $this->entry;
    }

    /**
     * Convert response to a flat associative payload (scalars only).
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
