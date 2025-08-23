<?php

declare(strict_types=1);

namespace Daylog\Application\DTO\Entries\ListEntries;

use Daylog\Application\DTO\Entries\ListEntries\ListEntriesRequestInterface;

/**
 * Immutable criteria DTO for UC-2 List Entries.
 *
 * Purpose:
 * Carry already-normalized parameters from the Request DTO to repositories,
 * without performing any additional normalization or business validation.
 * A stable secondary sort by `createdAt DESC` is always appended (AC-8).
 *
 * Mechanics:
 * - Builds from a normalized ListEntriesRequestInterface via fromRequest().
 * - Stores values as-is; validators handle business rules elsewhere.
 *
 * @psalm-type SortItem=array{field:string, direction:'ASC'|'DESC'}
 * @psalm-type SortList=array<int, SortItem>
 */
final class ListEntriesCriteria
{
    /** @var int */
    private int $page;

    /** @var int */
    private int $perPage;

    /** @var string|null YYYY-MM-DD or null (inclusive start) */
    private ?string $dateFrom;

    /** @var string|null YYYY-MM-DD or null (inclusive end) */
    private ?string $dateTo;

    /** @var string|null YYYY-MM-DD or null (exact logical date) */
    private ?string $date;

    /** @var string|null Normalized free-text query */
    private ?string $query;

    /** @var array<int, array{field:string, direction:'ASC'|'DESC'}> */
    private array $sort;

    /**
     * Factory: build criteria from a normalized transport request.
     *
     * No normalization is performed here. Values are copied as-is from the request,
     * and a stable secondary order is appended: ['createdAt' => 'DESC'].
     *
     * @param ListEntriesRequestInterface $req Normalized request DTO for UC-2.
     * @return self
     */
    public static function fromRequest(ListEntriesRequestInterface $req): self
    {
        $page     = $req->getPage();
        $perPage  = $req->getPerPage();
        $dateFrom = $req->getDateFrom();
        $dateTo   = $req->getDateTo();
        $date     = $req->getDate();
        $query    = $req->getQuery();

        $primaryField = $req->getSort();
        $primaryDir   = $req->getDirection();

        $secondaryField = 'createdAt';
        $secondaryDir   = 'DESC';

        $sort = [
            ['field' => $primaryField,  'direction' => $primaryDir],
            ['field' => $secondaryField,'direction' => $secondaryDir],
        ];

        $instance = new self($page, $perPage, $dateFrom, $dateTo, $date, $query, $sort);
        return $instance;
    }

    /**
     * @param int                                       $page
     * @param int                                       $perPage
     * @param string|null                               $dateFrom
     * @param string|null                               $dateTo
     * @param string|null                               $date
     * @param string|null                               $query
     * @param array<int, array{field:string, direction:'ASC'|'DESC'}> $sort
     */
    private function __construct(
        int $page,
        int $perPage,
        ?string $dateFrom,
        ?string $dateTo,
        ?string $date,
        ?string $query,
        array $sort
    ) {
        $this->page     = $page;
        $this->perPage  = $perPage;
        $this->dateFrom = $dateFrom;
        $this->dateTo   = $dateTo;
        $this->date     = $date;
        $this->query    = $query;
        $this->sort     = $sort;
    }

    /**
     * Current page (1-based).
     *
     * @return int
     */
    public function getPage(): int
    {
        $result = $this->page;
        return $result;
    }

    /**
     * Page size.
     *
     * @return int
     */
    public function getPerPage(): int
    {
        $result = $this->perPage;
        return $result;
    }

    /**
     * Inclusive range start (YYYY-MM-DD) or null.
     *
     * @return string|null
     */
    public function getDateFrom(): ?string
    {
        $result = $this->dateFrom;
        return $result;
    }

    /**
     * Inclusive range end (YYYY-MM-DD) or null.
     *
     * @return string|null
     */
    public function getDateTo(): ?string
    {
        $result = $this->dateTo;
        return $result;
    }

    /**
     * Exact logical date (YYYY-MM-DD) or null.
     *
     * @return string|null
     */
    public function getDate(): ?string
    {
        $result = $this->date;
        return $result;
    }

    /**
     * Normalized free-text query or null.
     *
     * @return string|null
     */
    public function getQuery(): ?string
    {
        $result = $this->query;
        return $result;
    }

    /**
     * Sorting descriptor for repositories.
     *
     * @return array<int, array{field:string, direction:'ASC'|'DESC'}>
     */
    public function getSortDescriptor(): array
    {
        $result = $this->sort;
        return $result;
    }
}
