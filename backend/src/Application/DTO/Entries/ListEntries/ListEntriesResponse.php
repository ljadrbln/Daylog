<?php
declare(strict_types=1);

namespace Daylog\Application\DTO\Entries\ListEntries;

use Daylog\Application\DTO\Entries\ListEntries\ListEntriesItem;

/**
 * Response DTO for UC-2 List Entries.
 *
 * Purpose:
 * Carry read-model items with pagination metadata and provide a scalar-only
 * payload for Presentation via toArray().
 *
 * Mechanics:
 * - Construct via fromArray() using repository output.
 * - Keep immutable state and typed getters for Application/tests.
 *
 * @implements ListEntriesResponseInterface
 */
final class ListEntriesResponse implements ListEntriesResponseInterface
{
    /**
     * @param list<ListEntriesItem> $items
     * @param int                   $page
     * @param int                   $perPage
     * @param int                   $total
     * @param int                   $pagesCount
     */
    private function __construct(
        private array $items,
        private int $page,
        private int $perPage,
        private int $total,
        private int $pagesCount
    ) {}

    /**
     * {@inheritDoc}
     */
    public static function fromArray(array $data): self
    {
        /** @var list<ListEntriesItem> $items */
        $items = [];

        foreach ($data['items'] as $entry) {
            $item = ListEntriesItem::fromEntry($entry);
            $items[] = $item;
        }

        $page       = $data['page'];
        $perPage    = $data['perPage'];
        $total      = $data['total'];
        $pagesCount = $data['pagesCount'];

        $response = new self($items, $page, $perPage, $total, $pagesCount);
        return $response;
    }

    /**
     * Convert response to a flat associative payload (scalars only).
     *
     * @return array{
     *   items: list<array{
     *     id: string,
     *     title: string,
     *     body: string,
     *     date: string,
     *     createdAt: string,
     *     updatedAt: string
     *   }>,
     *   page: int,
     *   perPage: int,
     *   total: int,
     *   pagesCount: int
     * }
     */
    public function toArray(): array
    {
        /** @var list<array{id:string,title:string,body:string,date:string,createdAt:string,updatedAt:string}> $items */
        $items = [];
        foreach ($this->items as $item) {
            $row   = $item->toArray();
            $items[] = $row;
        }

        $payload = [
            'items'      => $items,
            'page'       => $this->page,
            'perPage'    => $this->perPage,
            'total'      => $this->total,
            'pagesCount' => $this->pagesCount,
        ];
        return $payload;
    }

    /** @return list<ListEntriesItem> */
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
