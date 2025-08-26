<?php

declare(strict_types=1);

namespace Daylog\Tests\Support\Fixture;

use DB\SQL;
use Daylog\Domain\Services\UuidGenerator;

/**
 * Fixture for preparing Entry rows in the database during integration tests.
 *
 * Purpose:
 *  Provide a simple way to seed the `entries` table with minimal boilerplate.
 *  Tests specify only the logical dates (and optionally override title/body).
 *  UUIDs and timestamps are auto-generated to ensure validity.
 *
 * Mechanics:
 *  - id: UuidGenerator::generate()
 *  - created_at = updated_at = date + " 10:00:00"
 *  - title/body: defaults unless overridden in the spec
 *
 * Typical usage:
 *  $rows = EntryFixture::insertByDates(
 *      $db,
 *      ['2025-08-10','2025-08-11'],
 *      'Valid title',
 *      'Valid body'
 *  );
 */
final class EntryFixture
{
    /** @var SQL|null Shared DB instance for convenience methods */
    private static ?SQL $db = null;

    /**
     * Set shared DB instance used by convenience methods.
     *
     * @param SQL $db Active SQL connection.
     * @return void
     */
    public static function setDb(SQL $db): void
    {
        self::$db = $db;
        return;
    }

    /**
     * Insert N rows with dates generated from a deterministic base.
     *
     * Scenario:
     * - Quickly seed a number of entries for integration tests.
     * - Dates are spaced by $step days: base, base+step, base+2*step, ...
     * Mechanics:
     * - Builds a list of YYYY-MM-DD dates and delegates to insertByDates().
     *
     * @param int     $numberOfRows Positive number of rows to insert (≥ 1).
     * @param int     $step         Day step between consecutive dates (can be 0).
     * @return array<int,Row>       Inserted rows with generated UUIDs.
     */
    public static function insertRows(int $numberOfRows, int $step = 0): array
    {
        if ($numberOfRows < 1) {
            $message = 'numberOfRows must be >= 1.';
            throw new \InvalidArgumentException($message);
        }

        $baseDate = date('Y-m-d');
        $title    = 'Valid title';
        $body     = 'Valid body';

        for ($i = 0; $i < $numberOfRows; $i++) {
            $delta   = $i * $step;
            $ts      = strtotime($baseDate . ' +' . $delta . ' day');
            $dateStr = date('Y-m-d', $ts);

            $row = self::insertRow($dateStr, $title, $body);
            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * Insert entries into DB by logical dates.
     *
     * @param string                  $dateStr      Date in YYYY-MM-DD format
     * @param string                  $defaultTitle Title to use unless overridden
     * @param string                  $defaultBody  Body to use unless overridden
     * @return array<string,string>   Inserted rows with generated UUIDs
     */
    private static function insertRow(string $dateStr, string $defaultTitle, string $defaultBody): array
    {
        $id = UuidGenerator::generate();
        $ts = sprintf('%s 10:00:00', $dateStr);
        
        $row = [
            'id'         => $id,
            'title'      => $defaultTitle,
            'body'       => $defaultBody,
            'date'       => $dateStr,
            'created_at' => $ts,
            'updated_at' => $ts,
        ];

        $sql = 'INSERT INTO entries (id, title, body, date, created_at, updated_at) 
                VALUES (:id, :title, :body, :date, :created_at, :updated_at)';

        self::$db->exec($sql, $row);

        return $row;
    }    


   /**
     * Update allowed fields of an entry by id.
     *
     * @param string                 $id     UUID v4 of the entry to update.
     * @param array<string,string>   $patch  Allowed keys: 'title', 'body', 'date', 'created_at', 'updated_at'.
     * @return void
     */
    public static function updateById(string $id, array $patch): void
    {
        $allowed = ['title', 'body', 'date', 'created_at', 'updated_at'];

        $setParts = [];
        $params   = [];

        foreach ($allowed as $field) {
            $hasKey = array_key_exists($field, $patch);

            if ($hasKey) {
                $part = $field . ' = ?';
                $setParts[] = $part;

                $value = $patch[$field];
                $params[] = $value;
            }
        }

        $isEmpty = count($setParts) === 0;

        if ($isEmpty) {
            $message = 'No updatable fields provided. Allowed: title, body, date, created_at, updated_at.';
            throw new \InvalidArgumentException($message);
        }

        $setClause = implode(', ', $setParts);

        $sql = 'UPDATE entries SET ' . $setClause . ' WHERE id = ?';
        $params[] = $id;

        self::$db->exec($sql, $params);
        return;
    }    
}
