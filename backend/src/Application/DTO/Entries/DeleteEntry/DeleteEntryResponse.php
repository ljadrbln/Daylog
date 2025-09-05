<?php
declare(strict_types=1);

namespace Daylog\Application\DTO\Entries\DeleteEntry;

use Daylog\Application\DTO\Entries\DeleteEntry\DeleteEntryResponseInterface;

/**
 * Concrete response DTO for UC-4 DeleteEntry.
 *
 * Purpose:
 * Provide a simple acknowledgment containing the deleted UUID.
 *
 * Mechanics:
 * - Immutable value object with a single getter.
 */
final class DeleteEntryResponse implements DeleteEntryResponseInterface
{
    /** @var string */
    private string $id;

    /**
     * @param string $id UUID that was deleted.
     */
    public function __construct(string $id)
    {
        $value = $id;
        $this->id = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function getId(): string
    {
        $value = $this->id;
        return $value;
    }
}
