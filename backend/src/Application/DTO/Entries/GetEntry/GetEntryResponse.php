<?php
declare(strict_types=1);

namespace Daylog\Application\DTO\Entries\GetEntry;

use Daylog\Domain\Models\Entries\Entry;

/**
 * Concrete response DTO for UC-3 GetEntry.
 *
 * Purpose:
 * Provide the retrieved Entry in an immutable wrapper.
 *
 * Mechanics:
 * - Constructed with a domain Entry instance.
 * - Exposes a getter to access the Entry.
 */
final class GetEntryResponse implements GetEntryResponseInterface
{
    /** @var Entry */
    private Entry $entry;

    /**
     * @param Entry $entry Domain model retrieved from repository.
     */
    public function __construct(Entry $entry)
    {
        $value = $entry;
        $this->entry = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function getEntry(): Entry
    {
        $value = $this->entry;
        return $value;
    }
}
