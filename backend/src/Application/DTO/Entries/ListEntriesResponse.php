<?php

declare(strict_types=1);

namespace Daylog\Application\DTO\Entries;

/**
 * Response DTO for UC-2 List Entries.
 *
 * Holds the resulting items and pagination metadata.
 *
 * @template T of object
 */
final class ListEntriesResponse
{
    /** @var array<int, T> */
    private array $items;
    private int $page;
    private int $perPage;
    private int $total;
    private int $pagesCount;

    /**
     * @param array<int, T> $items      List of entries (may be empty).
     * @param int           $page       Current page.
     * @param int           $perPage    Items per page.
     * @param int           $total      Total items count.
     * @param int           $pagesCount Total number of pages.
     */
    public function __construct(
        array $items,
        int $page,
        int $perPage,
        int $total,
        int $pagesCount
    ) {
        $this->items      = $items;
        $this->page       = $page;
        $this->perPage    = $perPage;
        $this->total      = $total;
        $this->pagesCount = $pagesCount;
    }

    /**
     * @return array<int, T>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getPagesCount(): int
    {
        return $this->pagesCount;
    }
}
