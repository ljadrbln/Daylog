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
     * Constructor with optional parameters and defaults.
     *
     * @param string|null $dateFrom Lower bound for date filter (YYYY-MM-DD).
     * @param string|null $dateTo   Upper bound for date filter (YYYY-MM-DD).
     * @param string|null $date     Exact date filter (YYYY-MM-DD).
     * @param string|null $query    Substring search in title/body.
     * @param int         $page     Page number (default 1).
     * @param int         $perPage  Items per page (default 10).
     * @param string      $sort     Field to sort by (default "date").
     * @param string      $direction Sort direction ASC|DESC (default DESC).
     */
    public function __construct(
        ?string $dateFrom = null,
        ?string $dateTo = null,
        ?string $date = null,
        ?string $query = null,
        int $page = 1,
        int $perPage = 10,
        string $sort = 'date',
        string $direction = 'DESC'
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
