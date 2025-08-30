<?php

declare(strict_types=1);

namespace Daylog\Tests\Support\Helper;

use Daylog\Application\DTO\Entries\ListEntries\ListEntriesRequest;
use Daylog\Application\Normalization\Entries\ListEntriesInputNormalizer;
use Daylog\Domain\Models\Entries\ListEntriesConstraints;

/**
 * Helper for creating valid ListEntries request payloads in tests.
 *
 * Purpose:
 *  - Provide a baseline transport payload for ListEntriesRequest::fromArray().
 *  - Reduce duplication in tests by offering small builders for optional filters.
 *
 * Typical usage:
 *  $data = ListEntriesHelper::getData(2, 20, 'updatedAt', 'ASC');
 *  $data = ListEntriesHelper::withFilters($data, [
 *      'dateFrom' => '2025-08-01',
 *      'dateTo'   => '2025-08-21',
 *      'date'     => '2025-08-15',
 *      'query'    => 'notes',
 *  ]);
 *  $request = ListEntriesHelper::buildRequest($data);
 */

final class ListEntriesHelper
{
    /**
     * Return a valid baseline payload for ListEntriesRequest.
     *
     * Only required transport fields are included by default. Optional filters
     * (dateFrom, dateTo, date, query) are omitted to reflect their nullability.
     *
     * @param int    $page      Default page number
     * @param int    $perPage   Default items per page
     * @param string $sortField Default sort field: 'date'|'createdAt'|'updatedAt'
     * @param string $sortDir   Default sort direction: 'ASC'|'DESC'
     *
     * @return array{
     *     page:int,
     *     perPage:int,
     *     sortField:'createdAt'|'date'|'updatedAt',
     *     sortDir:'ASC'|'DESC'
     * }
     */
    public static function getData(
        int $page           = ListEntriesConstraints::PAGE_MIN,
        int $perPage        = ListEntriesConstraints::PER_PAGE_DEFAULT,
        string $sortField   = ListEntriesConstraints::SORT_FIELD_DEFAULT,
        string $sortDir     = ListEntriesConstraints::SORT_DIR_DEFAULT
    ): array {
        /** @var array{
         *   page:int,
         *   perPage:int,
         *   sortField:'createdAt'|'date'|'updatedAt',
         *   sortDir:'ASC'|'DESC'
         * } $data
         */

        $data = [
            'page'      => $page,
            'perPage'   => $perPage,
            'sortField' => $sortField,
            'sortDir'   => $sortDir,
        ];

        return $data;
    }

    /**
     * Merge optional filters into a baseline payload.
     *
     * Supported keys: dateFrom, dateTo, date, query.
     * Unknown keys are ignored to keep transport shape explicit.
     *
     * @param array{
     *   page:int,
     *   perPage:int,
     *   sortField:'createdAt'|'date'|'updatedAt',
     *   sortDir:'ASC'|'DESC'
     * } $base
     *
     * @param array{
     *   dateFrom?:string,
     *   dateTo?:string,
     *   date?:string,
     *   query?:string
     * } $filters
     * 
     * @return array{
     *   page:int,
     *   perPage:int,
     *   sortField:'createdAt'|'date'|'updatedAt',
     *   sortDir:'ASC'|'DESC',
     *   dateFrom?:string,
     *   dateTo?:string,
     *   date?:string,
     *   query?:string
     * }
     */
    public static function withFilters(array $base, array $filters): array
    {
        $allowed = ['dateFrom', 'dateTo', 'date', 'query'];

        foreach ($allowed as $key) {
            if (array_key_exists($key, $filters)) {
                $base[$key] = $filters[$key];
            }
        }

        /** @var array{
         *   page:int,
         *   perPage:int,
         *   sortField:'createdAt'|'date'|'updatedAt',
         *   sortDir:'ASC'|'DESC',
         *   dateFrom?:string,
         *   dateTo?:string,
         *   date?:string,
         *   query?:string
         * } $base
         */        
        return $base;
    }

    /**
     * Convenience: build a concrete ListEntriesRequest from payload.
     *
     * @param array{
     *   page:int,
     *   perPage:int,
     *   sortField:'createdAt'|'date'|'updatedAt',
     *   sortDir:'ASC'|'DESC',
     *   dateFrom?:string,
     *   dateTo?:string,
     *   date?:string,
     *   query?:string
     * } $data
     *
     * @return ListEntriesRequest
     */
    public static function buildRequest(array $data): ListEntriesRequest
    {
        $request = ListEntriesRequest::fromArray($data);        

        return $request;
    }
}
