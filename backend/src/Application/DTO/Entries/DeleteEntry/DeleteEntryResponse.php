<?php
declare(strict_types=1);

namespace Daylog\Application\DTO\Entries\DeleteEntry;

use Daylog\Domain\Models\Entries\Entry;
use Daylog\Application\Transformers\Entries\EntryTransformer;

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
 * @implements UseCaseResponseInterface<array{
 *   id: string,
 *   title: string,
 *   body: string,
 *   date: string,
 *   createdAt: string,
 *   updatedAt: string
 * }>
 */
final class DeleteEntryResponse implements DeleteEntryResponseInterface
{
    /**
     * @param Entry $entry Deleted domain entity snapshot.
     */
    private function __construct(
        private Entry $entry
    ) {}

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
        $entry   = $this->entry;
        $payload = EntryTransformer::fromEntry($entry);

        return $payload;
    }
}
