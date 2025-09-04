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
        self::validatePage($params);
        self::validatePerPage($params);
        self::validateSortField($params);
        self::validateSortDir($params);
        self::validateDateFrom($params);
        self::validateDateTo($params);
        self::validateDate($params);
        self::validateQuery($params);

        $request = ListEntriesRequest::fromArray($params);
        return $request;
    }

    /**
     * Validate page param: must be numeric if provided.
     *
     * @param array<string,mixed> $params
     * @return void
     */
    private static function validatePage(array $params): void
    {
        $raw = $params['page'] ?? null;

        if (!is_null($raw) && !is_numeric($raw)) {
            $errorCode = 'PAGE_MUST_BE_NUMERIC';
            $exception = new TransportValidationException($errorCode);

            throw $exception;
        }
    }

    /**
     * Validate perPage param: must be numeric if provided.
     *
     * @param array<string,mixed> $params
     * @return void
     */
    private static function validatePerPage(array $params): void
    {
        $raw = $params['perPage'] ?? null;

        if (!is_null($raw) && !is_numeric($raw)) {
            $errorCode = 'PER_PAGE_MUST_BE_NUMERIC';
            $exception = new TransportValidationException($errorCode);

            throw $exception;
        }
    }

    /**
     * Validate sort param: must be string if provided.
     *
     * @param array<string,mixed> $params
     * @return void
     */
    private static function validateSortField(array $params): void
    {
        $raw = $params['sortField'] ?? null;

        if (!is_null($raw) && !is_string($raw)) {
            $errorCode = 'SORT_FIELD_MUST_BE_STRING';
            $exception = new TransportValidationException($errorCode);

            throw $exception;
        }
    }

    /**
     * Validate direction param: must be string if provided.
     *
     * @param array<string,mixed> $params
     * @return void
     */
    private static function validateSortDir(array $params): void
    {
        $raw = $params['sortDir'] ?? null;

        if (!is_null($raw) && !is_string($raw)) {
            $errorCode = 'DIRECTION_MUST_BE_STRING';
            $exception = new TransportValidationException($errorCode);

            throw $exception;
        }
    }

    /**
     * Validate dateFrom param: must be string if provided.
     *
     * @param array<string,mixed> $params
     * @return void
     */
    private static function validateDateFrom(array $params): void
    {
        $raw = $params['dateFrom'] ?? null;

        if (!is_null($raw) && !is_string($raw)) {
            $errorCode = 'DATE_FROM_MUST_BE_STRING';
            $exception = new TransportValidationException($errorCode);

            throw $exception;
        }
    }

    /**
     * Validate dateTo param: must be string if provided.
     *
     * @param array<string,mixed> $params
     * @return void
     */
    private static function validateDateTo(array $params): void
    {
        $raw = $params['dateTo'] ?? null;

        if (!is_null($raw) && !is_string($raw)) {
            $errorCode = 'DATE_TO_MUST_BE_STRING';
            $exception = new TransportValidationException($errorCode);

            throw $exception;
        }
    }

    /**
     * Validate date param: must be string if provided.
     *
     * @param array<string,mixed> $params
     * @return void
     */
    private static function validateDate(array $params): void
    {
        $raw = $params['date'] ?? null;

        if (!is_null($raw) && !is_string($raw)) {
            $errorCode = 'DATE_MUST_BE_STRING';
            $exception = new TransportValidationException($errorCode);

            throw $exception;
        }
    }

    /**
     * Validate query param: must be string if provided.
     *
     * @param array<string,mixed> $params
     * @return void
     */
    private static function validateQuery(array $params): void
    {
        $raw = $params['query'] ?? null;

        if (!is_null($raw) && !is_string($raw)) {
            $errorCode = 'QUERY_MUST_BE_STRING';
            $exception = new TransportValidationException($errorCode);

            throw $exception;            
        }
    }
}
