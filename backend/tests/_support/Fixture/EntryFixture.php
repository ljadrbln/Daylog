<?php

declare(strict_types=1);

namespace Daylog\Tests\Support\Fixture;

use Daylog\Configuration\Bootstrap\SqlFactory;
use Daylog\Infrastructure\Storage\Entries\EntryFieldMapper;
use Daylog\Tests\Support\Helper\EntryTestData;

/**
 * Fixture for preparing Entry rows in the database during integration tests.
 *
 * Purpose:
 *  Provide a concise way to seed the `entries` table with deterministic data.
 *  Tests pass only logical dates and, optionally, title/body overrides.
 *  UUIDs and timestamps are pre-generated to ensure validity.
 *
 * Mechanics:
 *  - Input row keys: id, title, body, date, createdAt, updatedAt (camelCase)
 *  - Mapper converts to DB snake_case keys for SQL binding
 *  - Connection is obtained via SqlFactory::get(), avoiding nullable state
 */
final class EntryFixture
{
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

        $dbRow = EntryFieldMapper::toDbRow($row);

        $db = SqlFactory::get();
        $db->exec($sql, $dbRow);

        return;
    }

    /**
     * Update allowed fields of an entry by id.
     *
     * Scenario:
     *  Integration tests may adjust an already-seeded entry (title/body/date/timestamps).
     *  Only whitelisted fields are eligible for update; the method builds a safe SET clause.
     *
     * Cases:
     *  - Empty patch → InvalidArgumentException with a clear message.
     *  - Non-whitelisted keys are ignored (only provided allowed keys are used).
     *
     * @param string               $id    UUID v4 of the entry to update.
     * @param array<string,string> $patch Allowed keys: 'title', 'body', 'date', 'created_at', 'updated_at'.
     * @return void
     *
     * @throws \InvalidArgumentException When no updatable fields are provided.
     */
    public static function updateById(string $id, array $patch): void
    {
        $allowed = ['title', 'body', 'date', 'created_at', 'updated_at'];

        $setParts = [];
        $params   = [];

        for ($i = 0; $i < count($allowed); $i++) {
            $field = $allowed[$i];
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

        $db = SqlFactory::get();
        $db->exec($sql, $params);

        return;
    }
}
