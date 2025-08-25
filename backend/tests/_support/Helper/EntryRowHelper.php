<?php

declare(strict_types=1);

namespace Daylog\Tests\Support\Helper;

use Daylog\Domain\Models\Entries\Entry;

/**
 * Helper for building raw storage rows from Entry objects.
 *
 * Intended for unit/integration tests where repository/storage
 * is mocked to return associative arrays instead of domain entities.
 */
final class EntryRowHelper
{
    /**
     * Build a single storage row.
     *
     * @param string $id   UUID v4 for the row.
     * @param Entry  $e    Domain entry (source of date/title/body).
     * @param string $ts   Timestamp for createdAt/updatedAt (UTC).
     * @return array<string,string>
     */
    public static function makeRow(string $id, Entry $e, string $ts): array
    {
        $row = [
            'id'        => $id,
            'date'      => $e->getDate(),
            'title'     => $e->getTitle(),
            'body'      => $e->getBody(),
            'createdAt' => $ts,
            'updatedAt' => $ts,
        ];

        return $row;
    }

    /**
     * Build multiple rows from parallel arrays.
     *
     * @param array<int,string> $ids
     * @param array<int,Entry>  $entries
     * @param array<int,string> $timestamps
     * @return array<int,array<string,string>>
     */
    public static function makeRows(array $ids, array $entries, array $timestamps): array
    {
        $rows = [];

        foreach ($entries as $i => $entry) {
            $id  = $ids[$i];
            $ts  = $timestamps[$i];

            $rows[] = self::makeRow($id, $entry, $ts);
        }

        return $rows;
    }
}
