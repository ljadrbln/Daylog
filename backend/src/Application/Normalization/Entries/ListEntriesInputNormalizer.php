<?php

declare(strict_types=1);

namespace Daylog\Application\Normalization\Entries;

use Daylog\Application\DTO\Entries\ListEntries\ListEntriesRequestInterface;
use Daylog\Domain\Models\Entries\ListEntriesConstraints;

/**
 * UC-2 input normalizer (GREEN, method-per-field, no is_scalar).
 *
 * Purpose:
 * Convert a transport-level DTO into a canonical map for UC-2 List Entries:
 * apply defaults, bounds clamping, allow-list checks, and empty-to-null for strings.
 *
 * Assumptions:
 * - Presentation layer already guaranteed: known keys are scalar or null.
 * - No business validation here (date format, query length); this is done elsewhere.
 *
 * Output shape:
 * - page:int (>= PAGE_MIN)
 * - perPage:int (within [PER_PAGE_MIN..PER_PAGE_MAX], default PER_PAGE_DEFAULT)
 * - sortField:string (allow-listed, default SORT_FIELD_DEFAULT)
 * - sortDir:'ASC'|'DESC' (allow-listed, default SORT_DIR_DEFAULT)
 * - date:?string ('' -> null)
 * - dateFrom:?string ('' -> null)
 * - dateTo:?string ('' -> null)
 * - query:?string (trimmed, '' -> null)
 */
final class ListEntriesInputNormalizer
{
    /**
     * Normalize raw input for UC-2 List Entries.
     *
     * @param ListEntriesRequestInterface $request Transport DTO (query/body).
     * @return array{
     *     page:int,
     *     perPage:int,
     *     sortField:string,
     *     sortDir:'ASC'|'DESC',
     *     date:?string,
     *     dateFrom:?string,
     *     dateTo:?string,
     *     query:?string
     * }
     */
    public static function normalize(ListEntriesRequestInterface $request): array
    {
        $page      = self::normalizePage($request);
        $perPage   = self::normalizePerPage($request);
        $sortField = self::normalizeSortField($request);
        $sortDir   = self::normalizeSortDir($request);
        $date      = self::normalizeDate($request);
        $dateFrom  = self::normalizeDateFrom($request);
        $dateTo    = self::normalizeDateTo($request);
        $query     = self::normalizeQuery($request);

        $result = [
            'page'      => $page,
            'perPage'   => $perPage,
            'sortField' => $sortField,
            'sortDir'   => $sortDir,
            'date'      => $date,
            'dateFrom'  => $dateFrom,
            'dateTo'    => $dateTo,
            'query'     => $query,
        ];

        return $result;
    }

    /**
     * Normalize page: default PAGE_MIN; anything below -> PAGE_MIN.
     *
     * @param ListEntriesRequestInterface $request
     * @return int
     */
    private static function normalizePage(ListEntriesRequestInterface $request): int
    {
        $page = $request->getPage();

        if ($page < ListEntriesConstraints::PAGE_MIN) {
            $page = ListEntriesConstraints::PAGE_MIN;
        }

        return $page;
    }

    /**
     * Normalize perPage: default PER_PAGE_DEFAULT; clamp into [MIN..MAX].
     *
     * @param ListEntriesRequestInterface $request
     * @return int
     */
    private static function normalizePerPage(ListEntriesRequestInterface $request): int
    {
        $perPage = $request->getPerPage();

        if ($perPage < ListEntriesConstraints::PER_PAGE_MIN) {
            $perPage = ListEntriesConstraints::PER_PAGE_MIN;
        } elseif ($perPage > ListEntriesConstraints::PER_PAGE_MAX) {
            $perPage = ListEntriesConstraints::PER_PAGE_MAX;
        }

        return $perPage;
    }

    /**
     * Normalize sort field: allow-list guard with default fallback.
     *
     * @param ListEntriesRequestInterface $request
     * @return string
     */
    private static function normalizeSortField(ListEntriesRequestInterface $request): string
    {
        $candidate = $request->getSort();
        $allowed   = ListEntriesConstraints::ALLOWED_SORT_FIELDS;

        $isAllowed = in_array($candidate, $allowed, true);
        $field     = $isAllowed 
            ? $candidate 
            : ListEntriesConstraints::SORT_FIELD_DEFAULT;

        return $field;
    }

    /**
     * Normalize sort direction: uppercase, allow-list guard with default fallback.
     *
     * @param ListEntriesRequestInterface $request
     * @return 'ASC'|'DESC'
     */
    private static function normalizeSortDir(ListEntriesRequestInterface $request): string
    {
        $direction = $request->getDirection();
        $direction = strtoupper($direction);

        $allowed   = ListEntriesConstraints::ALLOWED_SORT_DIRS;
        $isAllowed = in_array($direction, $allowed, true);

        if (!$isAllowed) {
            $direction = ListEntriesConstraints::SORT_DIR_DEFAULT;
        }

        return $direction;
    }

    /**
     * Normalize exact date: '' → null (no format/calendar checks here).
     *
     * @param ListEntriesRequestInterface $request
     * @return string|null
     */
    private static function normalizeDate(ListEntriesRequestInterface $request): ?string
    {
        $raw = $request->getDate();

        if ($raw === null || $raw === '') {
            return null;
        }

        return $raw;
    }

    /**
     * Normalize dateFrom (inclusive start): '' → null.
     *
     * @param ListEntriesRequestInterface $request
     * @return string|null
     */
    private static function normalizeDateFrom(ListEntriesRequestInterface $request): ?string
    {
        $raw = $request->getDateFrom();

        if ($raw === null || $raw === '') {
            return null;
        }

        return $raw;
    }

    /**
     * Normalize dateTo (inclusive start): '' → null.
     *
     * @param ListEntriesRequestInterface $request
     * @return string|null
     */
    private static function normalizeDateTo(ListEntriesRequestInterface $request): ?string
    {
        $raw = $request->getDateTo();

        if ($raw === null || $raw === '') {
            return null;
        }

        return $raw;
    }

    /**
     * Normalize query: trim again defensively and convert empty to null.
     * (Even though sanitizer trims strings, this guarantees idempotence.)
     *
     * @param ListEntriesRequestInterface $request
     * @return string|null
     */
    private static function normalizeQuery(ListEntriesRequestInterface $request): ?string
    {
        $raw = $request->getQuery();

        if ($raw === null || $raw === '') {
            return null;
        }

        return $raw;
    }
}
