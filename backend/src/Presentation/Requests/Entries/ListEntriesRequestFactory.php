<?php

declare(strict_types=1);

namespace Daylog\Presentation\Requests\Entries;

use Daylog\Application\DTO\Entries\ListEntriesRequest;
use Daylog\Application\Exceptions\TransportValidationException;

/**
 * Factory for UC-2 ListEntries request (transport-level only).
 *
 * Purpose:
 * Convert raw transport payload (query/body) into a ListEntriesRequest.
 * This factory performs ONLY type checks:
 * - integers: page, perPage;
 * - strings:  dateFrom, dateTo, date, query, sort, direction.
 *
 * It does not trim, normalize, or validate formats. Business validation
 * (date format, ranges, clamping) must be handled by the UC-2 validator.
 */
final class ListEntriesRequestFactory
{
    /**
     * Build ListEntriesRequest from a raw array with transport-level type checks.
     *
     * @param array<string, mixed> $params Raw transport payload.
     *
     * @return ListEntriesRequest Normalized DTO for the use case.
     *
     * @throws TransportValidationException When any provided field has a wrong type.
     */
    public static function createFromArray(array $params): ListEntriesRequest
    {
        /** Collect transport errors */
        $errors = [];

        /** Read raw values */
        $rawPage      = $params['page']      ?? null;
        $rawPerPage   = $params['perPage']   ?? null;
        $rawDateFrom  = $params['dateFrom']  ?? null;
        $rawDateTo    = $params['dateTo']    ?? null;
        $rawDate      = $params['date']      ?? null;
        $rawQuery     = $params['query']     ?? null;
        $rawSort      = $params['sort']      ?? null;
        $rawDirection = $params['direction'] ?? null;

        /** Type checks (transport-level only) */
        if (!is_int($rawPage)) {
            $errors[] = 'PAGE_MUST_BE_INT';
        }

        if (!is_int($rawPerPage)) {
            $errors[] = 'PER_PAGE_MUST_BE_INT';
        }

        if (!is_string($rawSort)) {
            $errors[] = 'SORT_MUST_BE_STRING';
        }
        
        if (!is_string($rawDirection)) {
            $errors[] = 'DIRECTION_MUST_BE_STRING';
        }

        if (!is_null($rawDateFrom) && !is_string($rawDateFrom)) {
            $errors[] = 'DATE_FROM_MUST_BE_STRING';
        }

        if (!is_null($rawDateTo) && !is_string($rawDateTo)) {
            $errors[] = 'DATE_TO_MUST_BE_STRING';
        }

        if (!is_null($rawDate) && !is_string($rawDate)) {
            $errors[] = 'DATE_MUST_BE_STRING';
        }

        if (!is_null($rawQuery) && !is_string($rawQuery)) {
            $errors[] = 'QUERY_MUST_BE_STRING';
        }

        /** Throw on transport errors */
        if ($errors !== []) {
            $exception = new TransportValidationException($errors);
            throw $exception;
        }

        // Call DTO factory method
        $data = [
            'dateFrom'  => $rawDateFrom,
            'dateTo'    => $rawDateTo,
            'date'      => $rawDate,
            'query'     => $rawQuery,
            'page'      => $rawPage,
            'perPage'   => $rawPerPage,
            'sort'      => $rawSort,
            'direction' => $rawDirection
        ];

        $request = ListEntriesRequest::fromArray($data);

        return $request;
    }
}
