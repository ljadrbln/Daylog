<?php

namespace Daylog\Tests\Support\Helper;

/**
 * Helper for creating valid ListEntries request payloads in tests.
 *
 * Use this to avoid duplication of the default valid payload when
 * constructing ListEntriesRequest via ListEntriesRequest::fromArray().
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
     * @param string $sort      Default sort field
     * @param string $direction Default sort direction
     *
     * @return array{
     *     page:int,
     *     perPage:int,
     *     sort:string,
     *     direction:string
     * }
     */
    public static function getData(
        int $page = 1,
        int $perPage = 10,
        string $sort = 'date',
        string $direction = 'DESC'
    ): array {
        $data = [
            'page'      => $page,
            'perPage'   => $perPage,
            'sort'      => $sort,
            'direction' => $direction,
        ];

        return $data;
    }
}
