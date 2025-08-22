<?php

declare(strict_types=1);

namespace Daylog\Presentation\Requests\Entries;

use Daylog\Application\DTO\Entries\ListEntries\ListEntriesRequest;
use Daylog\Application\DTO\Entries\ListEntries\ListEntriesRequestInterface;
use Daylog\Application\Normalization\Entries\ListEntriesInputNormalizer;
use Daylog\Application\Exceptions\TransportValidationException;

/**
 * Builds ListEntriesRequest DTO from raw transport input.
 *
 * Purpose:
 * - Perform transport-level type checks (scalars or null) and raise TransportValidationException on violations.
 * - Delegate UC-2 normalization to the application normalizer.
 * - Construct a typed DTO using explicit local variables (no inline literals).
 *
 * Notes:
 * - Normalizer is created locally; DI is not required in this setup.
 * - Business validation (formats, ranges) is out of scope for this factory.
 */
final class ListEntriesRequestFactory
{
    /**
     * Create a DTO from a raw associative map.
     *
     * Mechanics:
     * 1) Verify known fields are scalar or null (transport guarantee).
     * 2) Normalize via ListEntriesInputNormalizer.
     * 3) Pass normalized scalars to the request DTO.
     *
     * @param array<string,mixed> $params Raw transport map (e.g., query/body).
     * @return ListEntriesRequestInterface Typed request DTO for UC-2.
     *
     * @throws TransportValidationException When any known field is non-scalar.
     */
    public static function fromArray(array $params): ListEntriesRequestInterface
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

        $normalizer = new ListEntriesInputNormalizer();
        $normalized = $normalizer->normalize($params);
        
        $request = ListEntriesRequest::fromArray($normalized);

        return $request;
    }
}
