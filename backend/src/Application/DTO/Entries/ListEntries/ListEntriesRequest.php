<?php

declare(strict_types=1);

namespace Daylog\Application\DTO\Entries\ListEntries;
use Daylog\Application\DTO\Entries\ListEntries\ListEntriesRequestInterface;

/**
 * UC-2 List Entries request DTO (normalized).
 *
 * Purpose:
 * Carries normalized, typed parameters for listing entries across
 * application boundaries. Contains no transport parsing and no normalization.
 *
 * Notes:
 * - Values must be pre-normalized by Application layer (e.g., ListEntriesInputNormalizer).
 * - The object is immutable from the caller perspective (no setters).
 */
final class ListEntriesRequest implements ListEntriesRequestInterface
{
    /**
     * Private constructor. Use fromArray().
     *
     * Construct a DTO with already-normalized values.
     *
     * Intended usage: the Presentation factory performs transport checks and delegates
     * normalization to the Application normalizer, then passes values here unchanged.
     *
     * @param int      $page       1-based page index (raw transport value).
     * @param int      $perPage    Items per page (raw transport value).
     * @param string   $sortField  Sort field (e.g., 'date', 'createdAt', 'updatedAt').
     * @param string   $sortDir    Sort direction ('ASC'|'DESC').
     * @param ?string  $dateFrom   Inclusive lower bound date (YYYY-MM-DD) or null.
     * @param ?string  $dateTo     Inclusive upper bound date (YYYY-MM-DD) or null.
     * @param ?string  $date       Exact date filter (YYYY-MM-DD) or null.
     * @param ?string  $query      Case-insensitive substring filter for title/body or null.
     */
    private function __construct(
        private int $page,
        private int $perPage,
        private string $sortField,
        private string $sortDir,
        private ?string $dateFrom,
        private ?string $dateTo,
        private ?string $date,
        private ?string $query
    ) {}

    /**
     * Factory method to create a ListEntriesRequest from an associative array.
     *
     * This factory expects already-normalized values produced by the Presentation
     * factory + Application normalizer. Required keys must be present; optional
     * keys may be omitted and will default to null.
     *
     * @param array{
     *     page: int,
     *     perPage: int,
     *     sortField: 'date'|'createdAt'|'updatedAt',
     *     sortDir: 'ASC'|'DESC',
     *     dateFrom?: string,
     *     dateTo?: string,
     *     date?: string,
     *     query?: string
     * } $data Normalized request parameters.
     *
     * @return ListEntriesRequestInterface New instance created from the given array.
     */
    public static function fromArray(array $data): ListEntriesRequestInterface
    {
        $page      = $data['page'];
        $perPage   = $data['perPage'];
        $sortField = $data['sortField'];
        $sortDir   = $data['sortDir'];

        $dateFrom  = $data['dateFrom'] ?? null;
        $dateTo    = $data['dateTo']   ?? null;
        $date      = $data['date']     ?? null;
        $query     = $data['query']    ?? null;

        $request = new self(
            $page,
            $perPage,
            $sortField,
            $sortDir,
            $dateFrom,
            $dateTo,
            $date,
            $query
        );

        return $request;
    }

    /**
     * Get current page (1-based).
     *
     * @return int
     */
    public function getPage(): int
    {
        $result = $this->page;
        return $result;
    }

    /**
     * Get page size.
     *
     * @return int
     */
    public function getPerPage(): int
    {
        $result = $this->perPage;
        return $result;
    }

    /**
     * Get primary sort field.
     *
     * @return string
     */
    public function getSort(): string
    {
        $result = $this->sortField;
        return $result;
    }

    /**
     * Get sort direction.
     *
     * @return string 'ASC'|'DESC'
     */
    public function getDirection(): string
    {
        $result = $this->sortDir;
        return $result;
    }

    /**
     * Get exact logical date.
     *
     * @return string|null YYYY-MM-DD or null.
     */
    public function getDate(): ?string
    {
        $result = $this->date;
        return $result;
    }

    /**
     * Get inclusive range start.
     *
     * @return string|null YYYY-MM-DD or null.
     */
    public function getDateFrom(): ?string
    {
        $result = $this->dateFrom;
        return $result;
    }

    /**
     * Get inclusive range end.
     *
     * @return string|null YYYY-MM-DD or null.
     */
    public function getDateTo(): ?string
    {
        $result = $this->dateTo;
        return $result;
    }

    /**
     * Get normalized free-text query.
     *
     * @return string|null Trimmed text or null.
     */
    public function getQuery(): ?string
    {
        $result = $this->query;
        return $result;
    }
}
