<?php

declare(strict_types=1);

namespace Daylog\Application\DTO\Entries;
use Daylog\Application\DTO\Entries\ListEntriesResponseInterface;

/**
 * Response DTO for UC-2 List Entries.
 *
 * Holds the resulting items and pagination metadata.
 *
 * @template T of object
 */
final class ListEntriesResponse implements ListEntriesResponseInterface
{
    /** @var array<int, T> */
    private array $items;
    private int $page;
    private int $perPage;
    private int $total;
    private int $pagesCount;

    /**
     * Private constructor. Use fromArray().
     * 
     * @param array<int, T> $items      List of entries (may be empty).
     * @param int           $page       Current page.
     * @param int           $perPage    Items per page.
     * @param int           $total      Total items count.
     * @param int           $pagesCount Total number of pages.
     */
    private function __construct(
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
     * Factory method to create a response from an associative array.
     *
     * @param array<string,string> $data Keys: id, title, body, date, createdAt, updatedAt.
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $items      = $data['items']      ?? [];
        $page       = $data['page']       ?? 1;
        $perPage    = $data['perPage']    ?? 10;
        $total      = $data['total']      ?? 0;
        $pagesCount = $data['pagesCount'] ?? 1;

        return new self($items, $page, $perPage, $total, $pagesCount);
    }

    /**
     * @return array<int, T>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @return int
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @return int
     */
    public function getPagesCount(): int
    {
        return $this->pagesCount;
    }
}
