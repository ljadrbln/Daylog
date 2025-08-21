<?php

declare(strict_types=1);

namespace Daylog\Application\DTO\Entries;

use Daylog\Application\DTO\Entries\ListEntriesRequestInterface;

/**
 * Immutable criteria DTO for UC-2 List Entries.
 *
 * This DTO maps raw transport values into a normalized shape for repositories:
 * - page defaults to 1 when < 1;
 * - perPage defaults to 10 and is clamped to [1..100];
 * - dateFrom/dateTo/date: '' becomes null (no format validation here);
 * - query is trimmed; empty becomes null;
 * - sort field/direction are validated against an allow-list;
 * - stable secondary order is always ['createdAt' => 'DESC'].
 *
 * Business validation is out of scope and must be handled by a validator.
 */
final class ListEntriesCriteria
{
    /** @var string[] */
    private const ALLOWED_SORT_FIELDS = ['date', 'createdAt', 'updatedAt'];

    /** @var string[] */
    private const ALLOWED_SORT_DIRS = ['ASC', 'DESC'];

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
     * Factory: build criteria from a transport request.
     *
     * @param ListEntriesRequestInterface $req
     * @return self
     */
    public static function fromRequest(ListEntriesRequestInterface $req): self
    {
        $page     = self::normalizePage($req);
        $perPage  = self::normalizePerPage($req);
        $dateFrom = self::normalizeDateFrom($req);
        $dateTo   = self::normalizeDateTo($req);
        $date     = self::normalizeDate($req);
        $query    = self::normalizeQuery($req);

        [$primarySortField, $primarySortDir] = self::normalizeSort($req);

        $sort = [
            ['field' => $primarySortField, 'direction' => $primarySortDir],
            ['field' => 'createdAt',       'direction' => 'DESC']
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
    public function __construct(
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

    /**
     * Normalize page: default to 1 when < 1.
     *
     * @param ListEntriesRequestInterface $req
     * @return int
     */
    private static function normalizePage(ListEntriesRequestInterface $req): int
    {
        $page = $req->getPage();
        $page = (int)$page;

        if ($page < 1) {
            $page = 1;
        }

        return $page;
    }

    /**
     * Normalize perPage: default 10, clamp to [1..100].
     *
     * @param ListEntriesRequestInterface $req
     * @return int
     */
    private static function normalizePerPage(ListEntriesRequestInterface $req): int
    {
        $perPage = $req->getPerPage();
        $perPage = (int)$perPage;

        if ($perPage < 1) {
            $perPage = 10;
        } elseif ($perPage > 100) {
            $perPage = 100;
        }

        return $perPage;
    }

    /**
     * Normalize dateFrom: empty string -> null.
     *
     * @param ListEntriesRequestInterface $req
     * @return string|null
     */
    private static function normalizeDateFrom(ListEntriesRequestInterface $req): ?string
    {
        $dateFrom = $req->getDateFrom();
        $dateFrom = ($dateFrom === '') 
            ? null 
            : $dateFrom;
        
        return $dateFrom;
    }

    /**
     * Normalize dateTo: empty string -> null.
     *
     * @param ListEntriesRequestInterface $req
     * @return string|null
     */
    private static function normalizeDateTo(ListEntriesRequestInterface $req): ?string
    {
        $dateTo = $req->getDateTo();
        $dateTo = ($dateTo === '')
            ? null 
            : $dateTo;

        return $dateTo;
    }

    /**
     * Normalize exact date: empty string -> null.
     *
     * @param ListEntriesRequestInterface $req
     * @return string|null
     */
    private static function normalizeDate(ListEntriesRequestInterface $req): ?string
    {
        $date = $req->getDate();
        $date = ($date === '') 
            ? null 
            : $date;

        return $date;
    }

    /**
     * Normalize query: trim; empty -> null.
     *
     * @param ListEntriesRequestInterface $req
     * @return string|null
     */
    private static function normalizeQuery(ListEntriesRequestInterface $req): ?string
    {
        $queryRaw = $req->getQuery();
        $query    = is_string($queryRaw) 
            ? trim($queryRaw) 
            : null;

        $query    = ($query === '') 
            ? null 
            : $query;

        return $query;
    }

    /**
     * Normalize sorting pair (field, direction) with allow-lists and defaults.
     *
     * @param ListEntriesRequestInterface $req
     * @return array{0:string,1:'ASC'|'DESC'}
     */
    private static function normalizeSort(ListEntriesRequestInterface $req): array
    {
        $fieldRaw = $req->getSort();
        $field    = in_array($fieldRaw, self::ALLOWED_SORT_FIELDS, true) 
            ? $fieldRaw 
            : 'date';

        $dirRaw   = $req->getDirection();
        $dirUpper = strtoupper($dirRaw);
        $dir      = in_array($dirUpper, self::ALLOWED_SORT_DIRS, true) 
            ? $dirUpper 
            : 'DESC';

        return [$field, $dir];
    }
}
