<?php

declare(strict_types=1);

namespace Daylog\Presentation\Requests\Entries;

use Daylog\Application\DTO\Entries\ListEntries\ListEntriesRequest;
use Daylog\Application\DTO\Entries\ListEntries\ListEntriesRequestInterface;
use Daylog\Application\Exceptions\TransportValidationException;

/**
 * Builds ListEntriesRequest DTO from raw transport input.
 *
 * Purpose:
 * - Perform transport-level type checks (scalars or null) and raise TransportValidationException on violations.
 * - Construct a typed DTO using explicit local variables (no inline literals).
 *
 * Notes:
 * - Business validation (formats, ranges) is out of scope for this factory.
 */
final class ListEntriesRequestFactory
{
    /**
     * Factory for ListEntriesRequest from raw transport params.
     *
     * Performs transport-level validation only:
     * - Missing/null is allowed.
     * - Reject invalid types (non-numeric for page/perPage, non-string for strings).
     * - No business rules here.
     *
     * @param array<string,mixed> $params Raw transport map (e.g., query/body).
     * @return ListEntriesRequestInterface Typed request DTO for UC-2.
     *
     * @throws TransportValidationException When any known field is non-scalar.
    */
    public static function fromArray(array $params): ListEntriesRequestInterface
    {
        $errors = [];

        $errors = self::validatePage($params, $errors);
        $errors = self::validatePerPage($params, $errors);
        $errors = self::validateSortField($params, $errors);
        $errors = self::validateSortDir($params, $errors);
        $errors = self::validateDateFrom($params, $errors);
        $errors = self::validateDateTo($params, $errors);
        $errors = self::validateDate($params, $errors);
        $errors = self::validateQuery($params, $errors);

        if ($errors !== []) {
            throw new TransportValidationException($errors);
        }

        $request = ListEntriesRequest::fromArray($params);
        return $request;
    }

    /**
     * Validate page param: must be numeric if provided.
     *
     * @param array<string,mixed> $params
     * @param string[] $errors
     * @return string[]
     */
    private static function validatePage(array $params, array $errors): array
    {
        $raw = $params['page'] ?? null;

        if (!is_null($raw) && !is_numeric($raw)) {
            $errors[] = 'PAGE_MUST_BE_NUMERIC';
        }

        return $errors;
    }

    /**
     * Validate perPage param: must be numeric if provided.
     *
     * @param array<string,mixed> $params
     * @param string[] $errors
     * @return string[]
     */
    private static function validatePerPage(array $params, array $errors): array
    {
        $raw = $params['perPage'] ?? null;

        if (!is_null($raw) && !is_numeric($raw)) {
            $errors[] = 'PER_PAGE_MUST_BE_NUMERIC';
        }

        return $errors;
    }

    /**
     * Validate sort param: must be string if provided.
     *
     * @param array<string,mixed> $params
     * @param string[] $errors
     * @return string[]
     */
    private static function validateSortField(array $params, array $errors): array
    {
        $raw = $params['sortField'] ?? null;

        if (!is_null($raw) && !is_string($raw)) {
            $errors[] = 'SORT_FIELD_MUST_BE_STRING';
        }

        return $errors;
    }

    /**
     * Validate direction param: must be string if provided.
     *
     * @param array<string,mixed> $params
     * @param string[] $errors
     * @return string[]
     */
    private static function validateSortDir(array $params, array $errors): array
    {
        $raw = $params['sortDir'] ?? null;

        if (!is_null($raw) && !is_string($raw)) {
            $errors[] = 'DIRECTION_MUST_BE_STRING';
        }

        return $errors;
    }

    /**
     * Validate dateFrom param: must be string if provided.
     *
     * @param array<string,mixed> $params
     * @param string[] $errors
     * @return string[]
     */
    private static function validateDateFrom(array $params, array $errors): array
    {
        $raw = $params['dateFrom'] ?? null;

        if (!is_null($raw) && !is_string($raw)) {
            $errors[] = 'DATE_FROM_MUST_BE_STRING';
        }

        return $errors;
    }

    /**
     * Validate dateTo param: must be string if provided.
     *
     * @param array<string,mixed> $params
     * @param string[] $errors
     * @return string[]
     */
    private static function validateDateTo(array $params, array $errors): array
    {
        $raw = $params['dateTo'] ?? null;

        if (!is_null($raw) && !is_string($raw)) {
            $errors[] = 'DATE_TO_MUST_BE_STRING';
        }

        return $errors;
    }

    /**
     * Validate date param: must be string if provided.
     *
     * @param array<string,mixed> $params
     * @param string[] $errors
     * @return string[]
     */
    private static function validateDate(array $params, array $errors): array
    {
        $raw = $params['date'] ?? null;

        if (!is_null($raw) && !is_string($raw)) {
            $errors[] = 'DATE_MUST_BE_STRING';
        }

        return $errors;
    }

    /**
     * Validate query param: must be string if provided.
     *
     * @param array<string,mixed> $params
     * @param string[] $errors
     * @return string[]
     */
    private static function validateQuery(array $params, array $errors): array
    {
        $raw = $params['query'] ?? null;

        if (!is_null($raw) && !is_string($raw)) {
            $errors[] = 'QUERY_MUST_BE_STRING';
        }

        return $errors;
    }

}
