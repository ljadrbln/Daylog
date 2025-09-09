<?php
declare(strict_types=1);

namespace Daylog\Application\DTO\Entries\UpdateEntry;

use Daylog\Application\DTO\Common\UseCaseResponseInterface;
use Daylog\Domain\Models\Entries\Entry;

/**
 * Contract for UC-5 UpdateEntry response DTO.
 *
 * Purpose:
 * Define a unified, Presentation-friendly response for the UpdateEntry use case,
 * exposing the persisted domain Entry after update and guaranteeing a flat scalar
 * payload via UseCaseResponseInterface::toArray().
 *
 * Mechanics:
 * - Built from a domain entity via static fromEntry().
 * - getEntry() provides the domain snapshot for downstream layers.
 * - The inherited toArray() MUST return the exact payload shape declared below.
 *
 * Payload shape:
 * - id: string (UUID v4)
 * - title: string
 * - body: string
 * - date: string (YYYY-MM-DD)
 * - createdAt: string (ISO-8601 UTC)
 * - updatedAt: string (ISO-8601 UTC)
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
interface UpdateEntryResponseInterface extends UseCaseResponseInterface
{
    /**
     * Create a response DTO from a domain Entry.
     *
     * @param Entry $entry Updated domain entity (already persisted).
     * @return self
     */
    public static function fromEntry(Entry $entry): self;

    /**
     * Get the encapsulated domain Entry snapshot.
     *
     * @return Entry Domain snapshot of the updated entry.
     */
    public function getEntry(): Entry;
}
