<?php
declare(strict_types=1);

namespace Daylog\Application\DTO\Entries\GetEntry;

use Daylog\Domain\Models\Entries\Entry;

/**
 * Concrete response DTO for UC-3 GetEntry.
 *
 * Purpose:
 * Provide the retrieved Entry in an immutable wrapper,
 * with a scalar-only payload for Presentation.
 *
 * @implements GetEntryResponseInterface
 */
final class GetEntryResponse implements GetEntryResponseInterface
{
    private Entry $entry;

    /**
     * @param Entry $entry Domain model retrieved from repository.
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
     * {@inheritDoc}
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
