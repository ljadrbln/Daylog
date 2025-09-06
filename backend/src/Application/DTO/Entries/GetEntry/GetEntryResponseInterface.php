<?php
declare(strict_types=1);

namespace Daylog\Application\DTO\Entries\GetEntry;

use Daylog\Application\DTO\Common\UseCaseResponseInterface;
use Daylog\Domain\Models\Entries\Entry;

/**
 * Contract for UC-3 GetEntry response DTO.
 *
 * Purpose:
 * Define the boundary for delivering a single Entry to callers, and guarantee
 * a Presentation-friendly scalar payload.
 *
 * Mechanics:
 * - Built from a domain Entry via fromEntry().
 * - getEntry() exposes the domain snapshot.
 * - toArray() provides a flat associative payload with scalars only.
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
interface GetEntryResponseInterface extends UseCaseResponseInterface
{
    /**
     * Create response from a domain Entry.
     *
     * @param Entry $entry Retrieved domain model.
     * @return self
     */
    public static function fromEntry(Entry $entry): self;

    /**
     * @return Entry Domain snapshot of the retrieved entry.
     */
    public function getEntry(): Entry;
}
