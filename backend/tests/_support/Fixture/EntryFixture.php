<?php

declare(strict_types=1);

namespace Daylog\Tests\Support\Fixture;

use DB\SQL;
use Daylog\Infrastructure\Storage\Entries\EntryFieldMapper;
use Daylog\Tests\Support\Helper\EntryTestData;

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
    /** @var SQL Shared DB instance for convenience methods */
    private static SQL $db;

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
     * Clean the 'entries' table to ensure a deterministic state between tests.
     *
     * Purpose:
     *   Provide a single, reusable place to clear the table in integration tests,
     *   aligned with docs (no TRUNCATE scattered across tests).
     *
     * Mechanics:
     *   - If override DB is set via setDb(), use it.
     *
     * @return void
     */
    public static function cleanTable(): void
    {
        $db = self::getDb();

        $sql = 'DELETE FROM entries';
        $db->exec($sql);

        return;
    }

    /**
     * Insert N rows with deterministic dates for integration tests.
     *
     * The helper generates consistent rows; each row is persisted.
     *
     * @param int $numberOfRows Positive number (>= 1).
     * @param int $step         Day step between consecutive dates (can be 0).
     * @return array<int,array{
     *   id: string,
     *   date: string,
     *   title: string,
     *   body: string,
     *   createdAt: string,
     *   updatedAt: string
     * }>
     */  
    public static function insertRows(int $numberOfRows, int $step = 0): array
    {
        $rows = EntryTestData::getMany($numberOfRows, $step);

        for ($i = 0; $i < count($rows); $i++) {
            $row = $rows[$i];
            self::insertRow($row);
        }

        return $rows;
    }

    /**
     * Insert a single entry into DB using a logical row payload.
     *
     * The row is expected to contain valid logical fields:
     * - id, title, body, date, createdAt, updatedAt (all strings).
     * The method maps keys to DB shape and performs a parameterized INSERT.
     *
     * @param array<string,string> $row Logical entry data payload.
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
     * Scenario:
     *  Integration tests may adjust an already-seeded entry (title/body/date/timestamps).
     *  The method builds a safe SET clause.
     *
     * @param string               $id    UUID v4 of the entry to update.
     * @param array<string,string> $patch Keys: 'title', 'body', 'date', 'created_at', 'updated_at'.
     * @return void
     *
     * @throws \InvalidArgumentException When no updatable fields are provided.
     */
    public static function updateById(string $id, array $patch): void
    {
        $setParts      = [];
        $requestParams = [];

        foreach ($patch as $key => $value) {
            $part = sprintf('%s = ?', $key);
            $setParts[] = $part;
            
            $requestParams[] = $value;
        }

        $setClause = implode(', ', $setParts);

        $sql = 'UPDATE entries SET ' . $setClause . ' WHERE id = ?';
        $requestParams[] = $id;

        self::$db->exec($sql, $requestParams);
    }    
}