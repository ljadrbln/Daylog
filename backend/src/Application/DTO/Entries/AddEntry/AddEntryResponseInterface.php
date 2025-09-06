<?php
declare(strict_types=1);

namespace Daylog\Application\DTO\Entries\AddEntry;

use Daylog\Domain\Models\Entries\Entry;

/**
 * Contract for UC-1 AddEntry response DTO.
 *
 * Purpose:
 * Define a unified response for the AddEntry use case,
 * exposing the persisted domain Entry through a stable accessor.
 *
 * Mechanics:
 * - Built from a domain entity via static fromEntry().
 * - getEntry() provides the domain snapshot to Presentation/API layers.
 */
interface AddEntryResponseInterface
{
    /**
     * Create a response DTO from a domain Entry.
     *
     * @param Entry $entry Newly created domain entity (already persisted).
     * @return self
     */
    public static function fromEntry(Entry $entry): self;

    /**
     * @return Entry Domain snapshot of the added entry.
     */
    public function getEntry(): Entry;
}
