<?php
declare(strict_types=1);

namespace Daylog\Tests\Support\Helper;

/**
 * Class EntryHelper
 *
 * Provides valid camelCase payloads for Application/DTO layer.
 * Intended for validators, request factories and use cases.
 *
 * @return array{title:string, body:string, date:string, createdAt:string, updatedAt:string}
 */
final class EntryHelper
{
    /**
     * Build a valid DTO-like array (camelCase).
     *
     * @param string $title     Default valid title
     * @param string $body      Default valid body
     * @param string $date      Default valid date (YYYY-MM-DD)
     * @param string $createdAt Default createdAt (UTC, "Y-m-d H:i:s")
     * @param string $updatedAt Default updatedAt (UTC, "Y-m-d H:i:s")
     * @return array{title:string, body:string, date:string, createdAt:string, updatedAt:string}
     */
    public static function getData(
        string $title     = 'Valid title',
        string $body      = 'Valid body',
        string $date      = '2025-08-13',
        string $createdAt = '2025-08-13 12:00:00',
        string $updatedAt = '2025-08-13 12:00:00'
    ): array {
        $payload = [
            'title'     => $title,
            'body'      => $body,
            'date'      => $date,
            'createdAt' => $createdAt,
            'updatedAt' => $updatedAt,
        ];

        return $payload;
    }
}
