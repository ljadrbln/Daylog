<?php

declare(strict_types=1);

namespace Daylog\Application\DTO\Entries\ListEntries;

/**
 * Contract for UC-2 ListEntries request DTO.
 *
 * Purpose:
 * - Decouple the use-case from a concrete DTO implementation.
 * - Allow multiple factories (Presentation/UI) to produce compatible requests.
 *
 * Mechanics:
 * - All filters are optional (nullable); pagination/sort are required.
 * - No validation is implied here; validators operate elsewhere.
 */
interface ListEntriesRequestInterface
{
    /**
     * Inclusive lower bound date filter (YYYY-MM-DD) or null.
     *
     * @return string|null
     */
    public function getDateFrom(): ?string;

    /**
     * Inclusive upper bound date filter (YYYY-MM-DD) or null.
     *
     * @return string|null
     */
    public function getDateTo(): ?string;

    /**
     * Exact logical date filter (YYYY-MM-DD) or null.
     *
     * @return string|null
     */
    public function getDate(): ?string;

    /**
     * Case-insensitive substring for title/body or null.
     *
     * @return string|null
     */
    public function getQuery(): ?string;

    /**
     * 1-based page index.
     *
     * @return int
     */
    public function getPage(): int;

    /**
     * Items per page.
     *
     * @return int
     */
    public function getPerPage(): int;

    /**
     * Sort field (e.g., 'date', 'createdAt', 'updatedAt').
     *
     * @return string
     */
    public function getSort(): string;

    /**
     * Sort direction ('ASC'|'DESC').
     *
     * @return string
     */
    public function getDirection(): string;
}
