<?php
declare(strict_types=1);

namespace Daylog\Application\DTO\Entries\ListEntries;

use Daylog\Application\DTO\Common\UseCaseResponseInterface;

/**
 * Transport contract for UC-2 List Entries.
 *
 * Purpose:
 * Provide a Presentation-friendly response with read-model items and pagination
 * metadata, guaranteeing a flat scalar payload via toArray().
 *
 * Mechanics:
 * - Built from repository output via fromArray().
 * - Exposes typed getters for Application/tests.
 * - Serializes to scalars-only payload for Presentation.
 *
 * Payload shape:
 * - items: list<array{id,title,body,date,createdAt,updatedAt}>
 * - page, perPage, total, pagesCount: int
 *
 * @extends UseCaseResponseInterface<array{
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
 * }>
 */
interface ListEntriesResponseInterface extends UseCaseResponseInterface
{
    /**
     * Build response from normalized repository output.
     *
     * @param array{
     *   items: list<\Daylog\Domain\Models\Entries\Entry>,
     *   total: int,
     *   page: int,
     *   perPage: int,
     *   pagesCount: int
     * } $data
     * @return self
     */
    public static function fromArray(array $data): self;

    /**
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
