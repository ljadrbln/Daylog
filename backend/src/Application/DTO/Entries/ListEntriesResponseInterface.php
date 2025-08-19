<?php

declare(strict_types=1);

namespace Daylog\Application\DTO\Entries;

use Daylog\Domain\Models\Entries\Entry;

/**
 * Contract for UC-2 ListEntries response DTO.
 *
 * Enables the use case to return any implementation that exposes the same API,
 * simplifying testing and future evolution (e.g., different read-models).
 *
 * @template T of Entry
 */
interface ListEntriesResponseInterface
{
    /**
     * @return list<Entry> Items on the current page (domain models or read-models).
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
