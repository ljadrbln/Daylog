<?php

declare(strict_types=1);

namespace Daylog\Application\DTO\Entries;

use Daylog\Application\DTO\Entries\ListEntriesRequestInterface;

/**
 * Immutable criteria DTO for UC-2 List Entries.
 *
 * Purpose:
 *  This DTO carries normalized list parameters from the presentation layer
 *  toward the application/repository. At RED step it is only a skeleton used
 *  to remove "class not found" errors and keep tests focused on behavior.
 *
 * Mechanics (to be implemented in GREEN):
 *  - Map fields from ListEntriesRequestInterface.
 *  - Apply safe normalization (trim query, empty strings -> null).
 *  - Keep business validation outside (dedicated validator).
 *
 * @psalm-type SortRule=array{field:string, direction:'ASC'|'DESC'}
 */
final class ListEntriesCriteria
{
    /** @var int */
    private int $page;

    /** @var int */
    private int $perPage;

    /** @var string|null */
    private ?string $fromDate;

    /** @var string|null */
    private ?string $toDate;

    /** @var string|null */
    private ?string $query;

    /**
     * @var array<int, array{field:string, direction:'ASC'|'DESC'}>
     */
    private array $sort;

    /**
     * Factory: create criteria from a transport request.
     * RED step: returns an empty criteria; mapping is added later in GREEN.
     *
     * @param ListEntriesRequestInterface $req
     * @return self
     */
    public static function fromRequest(ListEntriesRequestInterface $req): self
    {
        $instance = new self();
        return $instance;
    }

    /**
     * Private constructor for immutability.
     * RED step: initialize with neutral defaults; refined in GREEN.
     */
    private function __construct()
    {
        $this->page     = 0;
        $this->perPage  = 0;
        $this->fromDate = null;
        $this->toDate   = null;
        $this->query    = null;
        $this->sort     = [];
    }

    /**
     * Current page (1-based). Value is refined in GREEN step.
     *
     * @return int
     */
    public function getPage(): int
    {
        $result = $this->page;
        return $result;
    }

    /**
     * Page size. Value is refined in GREEN step.
     *
     * @return int
     */
    public function getPerPage(): int
    {
        $result = $this->perPage;
        return $result;
    }

    /**
     * Inclusive start date (YYYY-MM-DD) or null.
     *
     * @return string|null
     */
    public function getFromDate(): ?string
    {
        $result = $this->fromDate;
        return $result;
    }

    /**
     * Inclusive end date (YYYY-MM-DD) or null.
     *
     * @return string|null
     */
    public function getToDate(): ?string
    {
        $result = $this->toDate;
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
     * Sorting descriptor. Empty at RED step; to be defined in GREEN.
     *
     * @return array<int, array{field:string, direction:'ASC'|'DESC'}>
     */
    public function getSortDescriptor(): array
    {
        $result = $this->sort;
        return $result;
    }
}
