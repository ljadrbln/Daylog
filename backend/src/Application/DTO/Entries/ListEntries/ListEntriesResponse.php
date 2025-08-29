<?php

declare(strict_types=1);

namespace Daylog\Application\DTO\Entries\ListEntries;

use Daylog\Application\DTO\Entries\ListEntries\ListEntriesItem;
use Daylog\Application\DTO\Entries\ListEntries\ListEntriesResponseInterface;

/**
 * Response DTO for UC-2 List Entries.
 *
 * Carries a list of ListEntriesItem and pagination metadata from the Use Case
 * to the Presentation layer. Construct via fromArray() using repository output.
 */
final class ListEntriesResponse implements ListEntriesResponseInterface
{
    /** @var list<ListEntriesItem> */
    private array $items;
    private int $page;
    private int $perPage;
    private int $total;
    private int $pagesCount;

    /**
     * Private constructor. Use fromArray().
     *
     * @param list<ListEntriesItem> $items    List of items (may be empty).
     * @param int                   $page     Current page (1-based).
     * @param int                   $perPage  Items per page.
     * @param int                   $total    Total items count.
     * @param int                   $pagesCount Total number of pages.
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
     * Factory method to create a response from repository output.
     *
     * @param array{
     *     items: list<Entry>,
     *     total: int,
     *     page: int,
     *     perPage: int,
     *     pagesCount: int
     * } $data Normalized repository result.
     *
     * @return self
     */

    public static function fromArray(array $data): self
    {
        /** @var list<ListEntriesItem> $items */
        $items = [];

        foreach ($data['items'] as $row) {
            $item = ListEntriesItem::fromArray($row);
            $items[] = $item;
        }

        $page       = $data['page'];
        $perPage    = $data['perPage'];
        $total      = $data['total'];
        $pagesCount = $data['pagesCount'];

        $response = new self(
            $items,
            $page,
            $perPage,
            $total,
            $pagesCount
        );

        return $response;
    }

    /**
     * @return list<ListEntriesItem>
     */
    public function getItems(): array
    {
        $items = $this->items;
        return $items;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        $page = $this->page;
        return $page;
    }

    /**
     * @return int
     */
    public function getPerPage(): int
    {
        $perPage = $this->perPage;
        return $perPage;
    }

    /**
     * @return int
     */
    public function getTotal(): int
    {
        $total = $this->total;
        return $total;
    }

    /**
     * @return int
     */
    public function getPagesCount(): int
    {
        $pagesCount = $this->pagesCount;
        return $pagesCount;
    }
}
