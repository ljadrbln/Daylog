<?php

declare(strict_types=1);

namespace Daylog\Application\Normalization\Entries;

use Daylog\Application\DTO\Entries\ListEntries\ListEntriesRequestInterface;
use Daylog\Domain\Models\Entries\ListEntriesConstraints;

/**
 * UC-2 input normalizer (GREEN, method-per-field, no is_scalar).
 *
 * Purpose:
 * Convert a raw transport map into a normalized shape for UC-2 List Entries.
 * Applies defaults, clamping, trimming, and empty-to-null conversions.
 *
 * Assumptions:
 * - The Presentation layer has already ensured that known keys are scalar or null.
 * - No business validation is performed here (date format, query length, etc.).
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
     * @param ListEntriesRequestInterface $request Raw transport map (e.g., $_GET or JSON).
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
     * Normalize page: default to ListEntriesConstraints::PAGE_MIN when < ListEntriesConstraints::PAGE_MIN.
     *
     * Assumes transport already ensured scalar|null for 'page'.
     * Missing/null -> PAGE_MIN; values below PAGE_MIN are corrected to PAGE_MIN.
     *
     * @param ListEntriesRequestInterface $request
     * @return int
     */
    private static function normalizePage(ListEntriesRequestInterface $request): int
    {
        $raw  = $request->getPage() ?? null;
        $page = ListEntriesConstraints::PAGE_MIN;

        if (!is_null($raw)) {
            $page = (int) $raw;
        }

        if ($page < ListEntriesConstraints::PAGE_MIN) {
            $page = ListEntriesConstraints::PAGE_MIN;
        }

        return $page;
    }

    /**
     * Normalize perPage: default PER_PAGE_DEFAULT, clamp to [PER_PAGE_MIN..PER_PAGE_MAX].
     *
     * Assumes scalar|null for 'perPage'. Missing/null -> PER_PAGE_DEFAULT.
     * Values below min are raised to PER_PAGE_MIN; above max are lowered to PER_PAGE_MAX.
     *
     * @param ListEntriesRequestInterface $request
     * @return int
     */
    private static function normalizePerPage(ListEntriesRequestInterface $request): int
    {
        $raw     = $request->getPerPage() ?? null;
        $perPage = ListEntriesConstraints::PER_PAGE_DEFAULT;

        if (!is_null($raw)) {
            $perPage = (int) $raw;
        }

        if ($perPage < ListEntriesConstraints::PER_PAGE_MIN) {
            $perPage = ListEntriesConstraints::PER_PAGE_MIN;
        } elseif ($perPage > ListEntriesConstraints::PER_PAGE_MAX) {
            $perPage = ListEntriesConstraints::PER_PAGE_MAX;
        }

        return $perPage;
    }

    /**
     * Normalize sort field: validate against allow-list, fallback to default.
     *
     * Assumes scalar/null for 'sortField'. Missing/null -> SORT_FIELD_DEFAULT.
     * If provided value is not in ALLOWED_SORT_FIELDS -> fallback to default.
     *
     * @param ListEntriesRequestInterface $request
     * @return string
     */
    private static function normalizeSortField(ListEntriesRequestInterface $request): string
    {
        $raw       = $request->getSort() ?? null;
        $candidate = ListEntriesConstraints::SORT_FIELD_DEFAULT;

        if (!is_null($raw)) {
            $candidate = (string) $raw;
        }

        $allowed   = ListEntriesConstraints::ALLOWED_SORT_FIELDS;
        $isAllowed = in_array($candidate, $allowed, true);

        $field = $candidate;
        if (!$isAllowed) {
            $field = ListEntriesConstraints::SORT_FIELD_DEFAULT;
        }

        return $field;
    }

    /**
     * Normalize sort direction: uppercase and validate; fallback to default.
     *
     * Assumes scalar/null for 'sortDir'. Missing/null -> SORT_DIR_DEFAULT.
     * Converts to uppercase and checks against ALLOWED_SORT_DIRS.
     * Invalid values fall back to SORT_DIR_DEFAULT.
     *
     * @param ListEntriesRequestInterface $request
     * @return 'ASC'|'DESC'
     */
    private static function normalizeSortDir(ListEntriesRequestInterface $request): string
    {
        $raw   = $request->getDirection() ?? null;
        $upper = ListEntriesConstraints::SORT_DIR_DEFAULT;

        if (!is_null($raw)) {
            $rawStr = (string) $raw;
            $upper  = strtoupper($rawStr);
        }

        $allowed   = ListEntriesConstraints::ALLOWED_SORT_DIRS;
        $isAllowed = in_array($upper, $allowed, true);

        $dir = $upper;
        if (!$isAllowed) {
            $dir = ListEntriesConstraints::SORT_DIR_DEFAULT;
        }

        return $dir;
    }

    /**
     * Normalize exact date: empty string -> null (no format/calendar checks).
     *
     * Assumes scalar/null for 'date'. Any non-empty string is passed through.
     *
     * @param ListEntriesRequestInterface $request
     * @return string|null
     */
    private static function normalizeDate(ListEntriesRequestInterface $request): ?string
    {
        $raw = $request->getDate() ?? null;
        $str = '';

        if (!is_null($raw)) {
            $str = (string) $raw;
        }

        $date = ($str === '') 
            ? null 
            : $str;

        return $date;
    }

    /**
     * Normalize dateFrom: empty string -> null (inclusive range start).
     *
     * Assumes scalar/null for 'dateFrom'. Any non-empty string is passed through.
     *
     * @param ListEntriesRequestInterface $request
     * @return string|null
     */
    private static function normalizeDateFrom(ListEntriesRequestInterface $request): ?string
    {
        $raw = $request->getDateFrom() ?? null;
        $str = '';

        if (!is_null($raw)) {
            $str = (string) $raw;
        }

        $dateFrom = ($str === '') 
            ? null 
            : $str;

        return $dateFrom;
    }

    /**
     * Normalize dateTo: empty string -> null (inclusive range end).
     *
     * Assumes scalar/null for 'dateTo'. Any non-empty string is passed through.
     *
     * @param ListEntriesRequestInterface $request
     * @return string|null
     */
    private static function normalizeDateTo(ListEntriesRequestInterface $request): ?string
    {
        $raw = $request->getDateTo() ?? null;
        $str = '';

        if (!is_null($raw)) {
            $str = (string) $raw;
        }

        $dateTo = ($str === '') 
            ? null 
            : $str;

        return $dateTo;
    }

    /**
     * Normalize query: trim and convert empty to null.
     *
     * Assumes scalar/null for 'query'. Trims whitespace; returns null when empty.
     * Matching semantics are repository concerns.
     *
     * @param ListEntriesRequestInterface $request
     * @return string|null
     */
    private static function normalizeQuery(ListEntriesRequestInterface $request): ?string
    {
        $raw = $request->getQuery() ?? null;
        $str = '';

        if (!is_null($raw)) {
            $str = (string) $raw;
        }

        $trimmed = trim($str);
        $query   = ($trimmed === '') 
            ? null 
            : $trimmed;

        return $query;
    }
}
