<?php
declare(strict_types=1);

namespace Daylog\Infrastructure\Storage\Entries;

use Daylog\Domain\Models\Entries\Entry;
use Daylog\Infrastructure\Utils\TimestampConverter;

/**
 * Minimal mapper between domain Entry and DB row.
 *
 * Only two directions are supported:
 * - Entry → snake_case DB row
 * - DB row (snake_case, SQL datetime) → camelCase array with ISO-8601 UTC
 */
final class EntryFieldMapper
{
    /**
     * Map Domain Entry into DB row.
     *
     * @param Entry $entry
     * @return array{
     *     id:string,
     *     title:string,
     *     body:string,
     *     date:string,
     *     created_at:string,
     *     updated_at:string
     * }
     */
    public static function toDbRowFromEntry(Entry $entry): array
    {
        $createdAt = $entry->getCreatedAt();
        $updatedAt = $entry->getUpdatedAt();
        
        $createdAt = TimestampConverter::isoToSqlUtc($createdAt);
        $updatedAt = TimestampConverter::isoToSqlUtc($updatedAt);
                
        $row = [
            'id'         => $entry->getId(),
            'title'      => $entry->getTitle(),
            'body'       => $entry->getBody(),
            'date'       => $entry->getDate(),
            'created_at' => $createdAt,
            'updated_at' => $updatedAt
        ];

        return $row;
    }

    /**
     * Map DB row (snake_case) to camelCase array.
     * Converts created_at/updated_at from SQL DATETIME (Y-m-d H:i:s)
     * to ISO-8601 UTC (Y-m-d\TH:i:sP).
     *
     * @param array{id:string,title:string,body:string,date:string,created_at:string,updated_at:string} $row
     * @return array{id:string,title:string,body:string,date:string,createdAt:string,updatedAt:string}
     */
    public static function fromDbRow(array $row): array
    {
        $createdAt = $row['created_at'];
        $updatedAt = $row['updated_at'];

        $createdAt = TimestampConverter::sqlToIsoUtc($createdAt);
        $updatedAt = TimestampConverter::sqlToIsoUtc($updatedAt);

        return [
            'id'        => $row['id'],
            'title'     => $row['title'],
            'body'      => $row['body'],
            'date'      => $row['date'],
            'createdAt' => $createdAt,
            'updatedAt' => $updatedAt
        ];
    }

    /**
     * Build F3 'order' string from domain descriptors (safe allow-list).
     *
     * Purpose:
     * Convert domain sort descriptors to a DB order clause while enforcing
     * field allow-list and direction allow-list to avoid invalid SQL tokens.
     *
     * @param array<int,array{field:string,direction:string}> $descriptors
     * @return string Comma-separated order clause with DB fields ('' when none valid).
     */
    public static function buildOrderSafe(array $descriptors): string
    {
        $map = [
            'date'      => 'date',
            'createdAt' => 'created_at',
            'updatedAt' => 'updated_at',
        ];
    
        $parts = [];
        foreach($descriptors as $descriptor) {
            $sortDir   = $descriptor['direction'];
            $sortField = $descriptor['field'];
            $sortField = $map[$sortField];

            $parts[] = sprintf('%s %s', $sortField, $sortDir);
        }
        
        $order = implode(', ', $parts);

        return $order;
    }    
}
