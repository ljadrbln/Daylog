<?php

declare(strict_types=1);

namespace Daylog\Application\UseCases\Entries;

use Daylog\Application\DTO\Entries\ListEntriesRequestInterface;
use Daylog\Application\DTO\Entries\ListEntriesResponseInterface;
use Daylog\Application\DTO\Entries\ListEntriesResponse;

use Daylog\Domain\Interfaces\Entries\EntryRepositoryInterface;
use Daylog\Domain\Models\Entries\Entry;

/**
 * UC-2: List Entries.
 *
 * Returns a paginated, deterministically sorted list of entries.
 * Default sort is by logical `date DESC` with a stable secondary order by `createdAt DESC` (AC-8).
 * Request may later include filters (date range, single date, query) and custom sort, which will be
 * added incrementally via TDD.
 *
 * @template T of Entry
 */
final class ListEntries
{
    private EntryRepositoryInterface $repository;

    /**
     * Constructor.
     *
     * @param EntryRepositoryInterface $repository Repository abstraction for entries fetching.
     */
    public function __construct(EntryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Execute the use case.
     *
     * Mechanics:
     * 1) Fetch all entries from repository.
     * 2) Sort by `date DESC`, then by `createdAt DESC` for stable order (AC-8).
     * 3) Apply simple pagination: 1-based page indexing, non-negative slicing.
     * 4) Build response DTO with items and metadata.
     *
     * @param ListEntriesRequestInterface  $request  Request parameters (filters, paging, sort).
     *
     * @return ListEntriesResponseInterface Response carrying items and pagination meta.
     */
    public function execute(ListEntriesRequestInterface $request): ListEntriesResponseInterface
    {
        /** Fetch all entries (filters/sort will be pushed down later when implemented) */
        $entries = $this->repository->fetchAll();

        /** Sort by date DESC with stable secondary order by createdAt DESC */
        $callable = [$this, 'compareByDateDesc'];
        usort($entries, $callable);

        /** Pagination inputs */
        $page    = $request->getPage();     // expected to be >= 1 by validator/DTO default
        $perPage = $request->getPerPage();  // expected to be within bounds (clamped elsewhere later)

        /** Compute slice boundaries (1-based page => 0-based offset) */
        $offset = ($page - 1) * $perPage;
        if ($offset < 0) {
            $offset = 0;
        }

        /** Total before slicing */
        $total = count($entries);

        /** Slice current page; empty slice is valid */
        $items = array_slice($entries, $offset, $perPage);

        /** Pages count (at least 1 page when total>0, else 0 to signal empty dataset) */
        $pagesFloat = $perPage > 0 ? ($total / $perPage) : 0;
        $pagesCount = $perPage > 0 ? (int)ceil($pagesFloat) : 0;

        /** Build response (no inline expressions in return) */
        $response = new ListEntriesResponse(
            $items,
            $page,
            $perPage,
            $total,
            $pagesCount
        );

        return $response;
    }

    /**
     * Comparator for entries: primary by logical date DESC, secondary by createdAt DESC.
     *
     * Assumptions:
     * - Entry::getDate() returns ISO date 'YYYY-MM-DD' (string) — string compare is safe.
     * - Entry::getCreatedAt() returns ISO-8601 datetime (string) — lexical compare preserves order.
     *
     * @param Entry $a Left operand.
     * @param Entry $b Right operand.
     *
     * @return int Negative if $a should go before $b, positive if after, zero if equal.
     */
    private function compareByDateDesc(Entry $a, Entry $b): int
    {
        $dateA = $a->getDate();
        $dateB = $b->getDate();

        $result = strcmp($dateB, $dateA);
        return $result;
    }
}
