<?php

declare(strict_types=1);

namespace Daylog\Application\Normalization\Entries;

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
     * @param array<string,mixed> $input Raw transport map (e.g., $_GET or JSON).
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
    public function normalize(ListEntriesRequestInterface $request): array
    {
        //use Daylog\Application\DTO\Entries\ListEntries\ListEntriesRequestInterface;
        $page      = $this->normalizePage($input);
        $perPage   = $this->normalizePerPage($input);
        $sortField = $this->normalizeSortField($input);
        $sortDir   = $this->normalizeSortDir($input);
        $date      = $this->normalizeDate($input);
        $dateFrom  = $this->normalizeDateFrom($input);
        $dateTo    = $this->normalizeDateTo($input);
        $query     = $this->normalizeQuery($input);

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
     * @param array<string,mixed> $input
     * @return int
     */
    private function normalizePage(array $input): int
    {
        $raw  = $input['page'] ?? null;
        $page = ListEntriesConstraints::PAGE_MIN;

        if (!is_null($raw)) {
            $page = (int)$raw;
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
     * @param array<string,mixed> $input
     * @return int
     */
    private function normalizePerPage(array $input): int
    {
        $raw     = $input['perPage'] ?? null;
        $perPage = ListEntriesConstraints::PER_PAGE_DEFAULT;

        if (!is_null($raw)) {
            $perPage = (int)$raw;
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
     * @param array<string,mixed> $input
     * @return string
     */
    private function normalizeSortField(array $input): string
    {
        $raw       = $input['sortField'] ?? null;
        $candidate = ListEntriesConstraints::SORT_FIELD_DEFAULT;

        if (!is_null($raw)) {
            $candidate = (string)$raw;
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
     * @param array<string,mixed> $input
     * @return 'ASC'|'DESC'
     */
    private function normalizeSortDir(array $input): string
    {
        $raw   = $input['sortDir'] ?? null;
        $upper = ListEntriesConstraints::SORT_DIR_DEFAULT;

        if (!is_null($raw)) {
            $rawStr = (string)$raw;
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
     * @param array<string,mixed> $input
     * @return string|null
     */
    private function normalizeDate(array $input): ?string
    {
        $raw = $input['date'] ?? null;
        $str = '';

        if (!is_null($raw)) {
            $str = (string)$raw;
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
     * @param array<string,mixed> $input
     * @return string|null
     */
    private function normalizeDateFrom(array $input): ?string
    {
        $raw = $input['dateFrom'] ?? null;
        $str = '';

        if (!is_null($raw)) {
            $str = (string)$raw;
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
     * @param array<string,mixed> $input
     * @return string|null
     */
    private function normalizeDateTo(array $input): ?string
    {
        $raw = $input['dateTo'] ?? null;
        $str = '';

        if (!is_null($raw)) {
            $str = (string)$raw;
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
     * @param array<string,mixed> $input
     * @return string|null
     */
    private function normalizeQuery(array $input): ?string
    {
        $raw = $input['query'] ?? null;
        $str = '';

        if (!is_null($raw)) {
            $str = (string)$raw;
        }

        $trimmed = trim($str);
        $query   = ($trimmed === '') 
            ? null 
            : $trimmed;

        return $query;
    }
}
