<?php

declare(strict_types=1);

namespace Daylog\Tests\Support\Fixture;

use DB\SQL;
use Daylog\Infrastructure\Storage\Entries\EntryFieldMapper;
use Daylog\Domain\Models\Entries\Entry;
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
            $entry = Entry::fromArray($row);

            self::insertEntry($entry);
        }

        return $rows;
    }

    /**
     * Insert a single entry row into the database.
     *
     * Purpose:
     *   Provide a convenient way for tests to persist one Entry without
     *   constructing the domain entity manually. Accepts primitive values
     *   (title, body, date), builds the Entry internally, maps it to DB
     *   format and executes an INSERT.
     *
     * Mechanics:
     *   - Generate a new UUID for the entry id.
     *   - Normalize title, body and date into a domain Entry instance.
     *   - Convert the Entry to a snake_case DB row via EntryFieldMapper.
     *   - Execute a parameterized INSERT statement on the shared test DB.
     *   - Return the generated id for further assertions in tests.
     *
     * Scenarios:
     *   - Used in integration tests to seed a single entry with specific values.
     *   - Typical case: AC happy-path preparation where exactly one entry is required.
     *
     * @param string $id    UUID v4 identifier of the newly inserted entry.
     * @param string $title Non-empty title string (expected to be valid per ENTRY-BR-1).
     * @param string $body  Non-empty body string (expected to be valid per ENTRY-BR-2).
     * @param string $date  Logical date in strict YYYY-MM-DD format (expected to be valid per ENTRY-BR-4).
     *
     * @return void
     */
    public static function insertOne(string $id, string $title, string $body, string $date): void {
        $payload = EntryTestData::getOne(title: $title, body: $body, date: $date);
        $payload['id'] = $id;
        
        $entry   = Entry::fromArray($payload);

        self::insertEntry($entry);
    }

    /**
     * Insert a single Entry entity into the database.
     *
     * Purpose:
     * - Accept a fully constructed domain Entry (id, title, body, date, createdAt, updatedAt).
     * - Convert it into a DB-compatible snake_case row via EntryFieldMapper.
     * - Execute a parameterized INSERT to persist the record.
     *
     * Notes:
     * - All business rules (BR-1..BR-2, ENTRY-BR-1..ENTRY-BR-4) are already enforced at higher layers (DTO + validators).
     * - This method performs no validation; it assumes the Entry is consistent.
     *
     * @param Entry $entry Domain entity containing logical and timestamp fields.
     * @return void
     */
    private static function insertEntry(Entry $entry): void
    {
        $sql = 'INSERT INTO entries (id, title, body, date, created_at, updated_at) 
                VALUES (:id, :title, :body, :date, :created_at, :updated_at)';

        // Normalize payload to DB shape (snake_case).
        $dbRow = EntryFieldMapper::toDbRowFromEntry($entry);

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

    /**
     * Count rows in the 'entries' table for assertions in integration tests.
     *
     * Purpose:
     * Provide a single-source row counter that works with the shared DB connection
     * registered through setDb(), avoiding ad-hoc PDO usage in tests.
     *
     * Mechanics:
     * - Requires a prior call to setDb() in test bootstrap/_before().
     * - Executes a simple COUNT(*) query and returns the integer result.
     *
     * @return int Number of rows currently present in 'entries'.
     */
    public static function countRows(): int
    {
        $sql = 'SELECT COUNT(*) AS cnt FROM entries';

        /** @var array<int,array<string,int|string>> $rows */
        $rows = self::$db->exec($sql);

        $count = $rows[0]['cnt'] ?? 0;
        $count = (int) $count;
        
        return $count;
    }
}