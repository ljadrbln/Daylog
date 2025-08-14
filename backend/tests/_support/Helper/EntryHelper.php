<?php

namespace Daylog\Tests\Helper;

/**
 * Helper for creating valid entry data arrays in tests.
 *
 * Use this to avoid duplication of the default valid payload
 * when constructing Entry instances via Entry::fromArray().
 */
final class EntryHelper
{
    /**
     * Return a valid entry data array.
     *
     * @param string $title Default valid title
     * @param string $body  Default valid body
     * @param string $date  Default valid date in YYYY-MM-DD format
     *
     * @return array{title:string, body:string, date:string}
     */
    public static function getData(
        string $title = 'Valid title',
        string $body  = 'Valid body',
        string $date  = '2025-08-13'
    ): array {
        $data = [
            'title' => $title,
            'body'  => $body,
            'date'  => $date,
        ];

        return $data;
    }
}
