<?php
declare(strict_types=1);

namespace Daylog\Application\DTO\Entries\DeleteEntry;

use Daylog\Application\DTO\Common\UseCaseResponseInterface;
use Daylog\Domain\Models\Entries\Entry;

/**
 * Response DTO contract for UC-4 Delete Entry.
 *
 * Purpose:
 * Define a unified Application-level response for successful deletion,
 * built from the deleted domain Entry and serializable for Presentation.
 *
 * Mechanics:
 * - Built via static factory fromEntry().
 * - Exposes getEntry() for internal usage.
 * - Provides toArray() for a flat associative payload (no transport details).
 *
 * @extends UseCaseResponseInterface<array{
 *   id: string,
 *   title: string,
 *   body: string,
 *   date: string,
 *   createdAt: string,
 *   updatedAt: string
 * }>
 */
interface DeleteEntryResponseInterface extends UseCaseResponseInterface
{
    /**
     * Build a response from the deleted domain Entry.
     *
     * @param Entry $entry Deleted entity snapshot.
     * @return self
     */
    public static function fromEntry(Entry $entry): self;

    /**
     * @return Entry Deleted entry snapshot for downstream usage.
     */
    public function getEntry(): Entry;
}
