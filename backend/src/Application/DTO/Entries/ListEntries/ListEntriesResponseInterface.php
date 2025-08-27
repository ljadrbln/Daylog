<?php

declare(strict_types=1);

namespace Daylog\Application\DTO\Entries\ListEntries;

/**
 * Transport contract for UC-2 List Entries.
 *
 * This interface is a presentation-facing contract that carries a read-model
 * (ListEntriesItem) and pagination metadata from the Use Case to the Presenter/View.
 * It is intentionally non-generic because this UC returns a fixed read-model.
 */
interface ListEntriesResponseInterface
{
    /**
     * Get the list of read-model items.
     *
     * @return list<ListEntriesItem> Non-associative list of immutable items.
     */
    public function getItems(): array;

    /** @return int Current 1-based page index. */
    public function getPage(): int;

    /** @return int Items per page. */
    public function getPerPage(): int;

    /** @return int Total number of items matching the request (unpaged). */
    public function getTotal(): int;

    /** @return int Total number of pages given total/perPage. */
    public function getPagesCount(): int;
}
