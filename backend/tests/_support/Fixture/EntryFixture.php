<?php

declare(strict_types=1);

namespace Daylog\Tests\Support\Fixture;

use DB\SQL;
use Daylog\Infrastructure\Storage\Entries\EntryFieldMapper;
use Daylog\Tests\Support\Helper\EntryRowHelper;

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
    }

    /**
     * Get shared DB instance used by convenience methods.
     *
     * @return SQL
     */
    public static function getDb(): SQL
    {
        return self::$db;
    }    

    /**
     * Insert N rows with dates generated from a deterministic base.
     *
     * @param int     $numberOfRows Positive number of rows to insert (≥ 1).
     * @param int     $step         Day step between consecutive dates (can be 0).
     * @return array<int,Row>       Inserted rows with generated UUIDs.
     */
    public static function insertRows(int $numberOfRows, int $step = 0): array
    {
        $rows = EntryRowHelper::generateRows($numberOfRows, $step);

        for ($i = 0; $i < count($rows); $i++) {
            $row = $rows[$i];
            self::insertRow($row);
        }

        return $rows;
    }

    /**
     * Insert entries into DB by logical dates.
     *
     * @param array $row Entry data row
     * @return void
     */
    private static function insertRow(array $row): void
    {
        $sql = 'INSERT INTO entries (id, title, body, date, created_at, updated_at) 
                VALUES (:id, :title, :body, :date, :created_at, :updated_at)';

        // Normalize payload to DB shape (snake_case).
        $dbRow = EntryFieldMapper::toDbRow($row);

        self::$db->exec($sql, $dbRow);
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
