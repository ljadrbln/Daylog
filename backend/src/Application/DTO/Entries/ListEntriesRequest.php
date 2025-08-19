<?php

declare(strict_types=1);

namespace Daylog\Application\DTO\Entries;
use Daylog\Application\DTO\Entries\ListEntriesRequestInterface;

/**
 * Request DTO for UC-2 List Entries.
 *
 * Contains filters, pagination, and sorting parameters.
 */
final class ListEntriesRequest implements ListEntriesRequestInterface
{
    public ?string $dateFrom;
    public ?string $dateTo;
    public ?string $date;
    public ?string $query;
    public int $page;
    public int $perPage;
    public string $sort;
    public string $direction;

   /**
     * Private constructor. Use fromArray().
     *
     * @param ?string $dateFrom   Inclusive lower bound date (YYYY-MM-DD) or null.
     * @param ?string $dateTo     Inclusive upper bound date (YYYY-MM-DD) or null.
     * @param ?string $date       Exact date filter (YYYY-MM-DD) or null.
     * @param ?string $query      Case-insensitive substring filter for title/body or null.
     * @param int     $page       1-based page index (raw transport value).
     * @param int     $perPage    Items per page (raw transport value).
     * @param string  $sort       Sort field (e.g., 'date', 'createdAt', 'updatedAt').
     * @param string  $direction  Sort direction ('ASC'|'DESC').
     */
    private function __construct(
        ?string $dateFrom,
        ?string $dateTo,
        ?string $date,
        ?string $query,
        int $page,
        int $perPage,
        string $sort,
        string $direction
    ) {
        $this->dateFrom  = $dateFrom;
        $this->dateTo    = $dateTo;
        $this->date      = $date;
        $this->query     = $query;
        $this->page      = $page;
        $this->perPage   = $perPage;
        $this->sort      = $sort;
        $this->direction = $direction;
    }

    /**
     * Factory method to create a ListEntriesRequest from an associative array.
     *
     * @param array<string,mixed> $data Input array with optional keys:
     *        dateFrom, dateTo, date, query, page, perPage, sort, direction.
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $dateFrom  = $data['dateFrom']  ?? null;
        $dateTo    = $data['dateTo']    ?? null;
        $date      = $data['date']      ?? null;
        $query     = $data['query']     ?? null;
        $page      = $data['page']      ?? 1;
        $perPage   = $data['perPage']   ?? 10;
        $sort      = $data['sort']      ?? 'date';
        $direction = $data['direction'] ?? 'DESC';

        $request = new self(
            $dateFrom,
            $dateTo,
            $date,
            $query,
            $page,
            $perPage,
            $sort,
            $direction
        );

        return $request;
    }

    /** @return string|null */
    public function getDateFrom(): ?string
    {
        $result = $this->dateFrom;
        return $result;
    }

    /** @return string|null */
    public function getDateTo(): ?string
    {
        $result = $this->dateTo;
        return $result;
    }

    /** @return string|null */
    public function getDate(): ?string
    {
        $result = $this->date;
        return $result;
    }

    /** @return string|null */
    public function getQuery(): ?string
    {
        $result = $this->query;
        return $result;
    }

    /** @return int */
    public function getPage(): int
    {
        $result = $this->page;
        return $result;
    }

    /** @return int */
    public function getPerPage(): int
    {
        $result = $this->perPage;
        return $result;
    }

    /** @return string */
    public function getSort(): string
    {
        $result = $this->sort;
        return $result;
    }

    /** @return string */
    public function getDirection(): string
    {
        $result = $this->direction;
        return $result;
    }
}
