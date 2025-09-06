<?php

declare(strict_types=1);

namespace Daylog\Application\Normalization\Entries\ListEntries;

/**
 * Contract for UC-2 input normalization.
 *
 * Purpose:
 * Provides a stable application-layer interface to transform raw
 * transport maps into a normalized shape expected by the request DTO.
 *
 * Method:
 * - normalize(): Applies defaults, clamps numeric parameters, trims strings,
 *   and converts empty strings to null for date fields. No business validation
 *   is performed here.
 *
 * @template TNormalized of array{
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
interface ListEntriesInputNormalizerInterface
{
    /**
     * Normalize raw transport input for UC-2 List Entries.
     *
     * @param array<string,mixed>   $input Raw map (e.g. parsed query/body).
     * @return TNormalized          Normalized map for constructing the request DTO.
     */
    public static function normalize(array $input): array;
}
