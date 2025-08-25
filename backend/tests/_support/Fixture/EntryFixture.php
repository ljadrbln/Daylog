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
    /**
     * Insert entries into DB by logical dates.
     *
     * @param SQL               $db           DB connection
     * @param array<int,string> $dates        List of dates in YYYY-MM-DD format
     * @param string            $defaultTitle Title to use unless overridden
     * @param string            $defaultBody  Body to use unless overridden
     * @return array<int,Row>   Inserted rows with generated UUIDs
     */
    public static function insertByDates(SQL $db, array $dates, string $defaultTitle, string $defaultBody): array
    {
        $rows = [];

        foreach ($dates as $date) {
            $id = UuidGenerator::generate();
            $ts = $date . ' 10:00:00';

            $row = [
                'id'         => $id,
                'title'      => $defaultTitle,
                'body'       => $defaultBody,
                'date'       => $date,
                'created_at' => $ts,
                'updated_at' => $ts,
            ];

            $rows[] = $row;
        }

        $sql = 'INSERT INTO entries (id, title, body, date, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)';
        foreach ($rows as $row) {
            $params = [
                $row['id'],
                $row['title'],
                $row['body'],
                $row['date'],
                $row['created_at'],
                $row['updated_at'],
            ];

            $db->exec($sql, $params);
        }

        return $rows;
    }

   /**
     * Update allowed fields of an entry by id.
     *
     * @param SQL                    $db     Database connection.
     * @param string                 $id     UUID v4 of the entry to update.
     * @param array<string,string>   $patch  Allowed keys: 'title', 'body', 'date', 'created_at', 'updated_at'.
     * @return void
     */
    public static function updateById(SQL $db, string $id, array $patch): void
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

        $db->exec($sql, $params);
        return;
    }    
}
