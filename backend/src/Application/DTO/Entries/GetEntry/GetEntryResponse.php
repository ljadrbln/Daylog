<?php
declare(strict_types=1);

namespace Daylog\Application\DTO\Entries\GetEntry;

use Daylog\Domain\Models\Entries\Entry;
use Daylog\Application\Transformers\Entries\EntryTransformer;

/**
 * Concrete response DTO for UC-3 GetEntry.
 *
 * Purpose:
 * Provide the retrieved Entry in an immutable wrapper,
 * with a scalar-only payload for Presentation.
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
final class GetEntryResponse implements GetEntryResponseInterface
{
    /**
     * @param Entry $entry Domain model retrieved from repository.
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
