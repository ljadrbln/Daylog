<?php

declare(strict_types=1);

namespace Daylog\Application\DTO\Entries;

/**
 * Request DTO for UC-2 List Entries.
 *
 * Contains filters, pagination, and sorting parameters.
 */
final class ListEntriesRequest
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


    /**
     * Return the requested page index (1-based).
     *
     * This accessor normalizes the value for safe consumption by the use case.
     * Defaults to 1 when the underlying value is null, zero, or negative.
     * Full bounds clamping (min/max) is handled by the UC-2 validator.
     *
     * @return int 1-based positive page index for pagination.
     */
    public function getPage(): int
    {
        /** @var int|null $raw */
        $raw = $this->page ?? null;

        $page = (int)($raw ?? 1);
        if ($page < 1) {
            $page = 1;
        }

        return $page;
    }

    /**
     * Return the number of items per page.
     *
     * This accessor provides a safe default suitable for typical UIs.
     * It does not enforce upper bounds; clamping is delegated to the UC-2 validator.
     *
     * @return int Positive items-per-page value (default: 10).
     */
    public function getPerPage(): int
    {
        /** @var int|null $raw */
        $raw = $this->perPage ?? null;

        $perPage = (int)($raw ?? 10);
        if ($perPage < 1) {
            $perPage = 10;
        }

        return $perPage;
    }
}
