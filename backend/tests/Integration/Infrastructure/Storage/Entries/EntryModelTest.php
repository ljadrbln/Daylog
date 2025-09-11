<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Infrastructure\Storage\Entries;

use Codeception\Test\Unit;
use Daylog\Domain\Models\Entries\Entry;
use Daylog\Domain\Services\UuidGenerator;
use Daylog\Infrastructure\Storage\Entries\EntryModel;
use Daylog\Configuration\Bootstrap\SqlFactory;
use Daylog\Infrastructure\Storage\Entries\EntryFieldMapper;
use DB\SQL;

use Daylog\Tests\Support\Helper\EntryTestData;

/**
 * Class EntryModelTest
 *
 * Integration test for EntryModel using a real database connection.
 * Database state is prepared by Codeception Db module with dump.sql.
 */
final class EntryModelTest extends Unit
{
    /**
     * @var SQL Database connection
     */
    private SQL $db;

    /**
     * Set up the database connection before each test.
     *
     * @return void
     */
    protected function _before(): void
    {
        $this->db = SqlFactory::get();

        $sql = 'DELETE FROM entries';
        $this->db->exec($sql);
    }

    /**
     * Insert-then-fetch roundtrip returns the same business fields.
     *
     * Scenario:
     * - Build a valid Entry via EntryTestData, map to DB payload with EntryFieldMapper.
     * - Persist with createEntry(), then read back with findById().
     *
     * Checks:
     * - Row is found (not null).
     * - title/body/date exactly match the inserted values.
     *
     * @return void
     * @covers \Daylog\Infrastructure\Storage\Entries\EntryModel::createEntry
     * @covers \Daylog\Infrastructure\Storage\Entries\EntryModel::findById
     */
    public function testInsertAndFetch(): void
    {
        // Arrange
        /** @var EntryModel $model */
        $model = new EntryModel($this->db);

        /** @var array<string,string> $data */
        $data  = EntryTestData::getOne();
        $entry = Entry::fromArray($data);

        $dbRow = EntryFieldMapper::toDbRowFromEntry($entry);
        $uuid = $data['id'];

        $model->createEntry($dbRow);

        // Act
        /** @var array<string,mixed>|null $fetched */
        $fetched = $model->findById($uuid);

        // Assert
        $this->assertNotNull($fetched);
        $this->assertSame($data['title'], $fetched['title']);
        $this->assertSame($data['body'],  $fetched['body']);
        $this->assertSame($data['date'],  $fetched['date']);
    }

    /**
     * findById() returns null for a missing UUID.
     *
     * Scenario:
     * - Generate a fresh UUID that is not present in the table.
     * - Call findById($uuid) without seeding state.
     *
     * Checks:
     * - Result is null.
     *
     * @return void
     * @covers \Daylog\Infrastructure\Storage\Entries\EntryModel::findById
     */
    public function testFindByIdReturnsNullWhenMissing(): void
    {
        // Arrange
        $model = new EntryModel($this->db);
        $missing = UuidGenerator::generate();

        // Act
        $row = $model->findById($missing);

        // Assert
        $this->assertNull($row);
    }    

    /**
     * updateEntry() modifies only provided fields for an existing row.
     *
     * Scenario:
     * - Insert a baseline row.
     * - updateEntry($id, ['title' => 'Updated title']), then fetch by id.
     *
     * Checks:
     * - title is updated; body and date remain unchanged.
     *
     * @return void
     * @covers \Daylog\Infrastructure\Storage\Entries\EntryModel::createEntry
     * @covers \Daylog\Infrastructure\Storage\Entries\EntryModel::updateEntry
     * @covers \Daylog\Infrastructure\Storage\Entries\EntryModel::findById
     */
    public function testUpdateEntryUpdatesExistingRow(): void
    {
        // Arrange
        $model = new EntryModel($this->db);

        $data  = EntryTestData::getOne();
        $entry = Entry::fromArray($data);
        $row   = EntryFieldMapper::toDbRowFromEntry($entry);

        $model->createEntry($row);

        $id = $data['id'];

        $payload = ['title' => 'Updated title'];
        
        // Act
        $model->updateEntry($id, $payload);
        $fetched = $model->findById($id);

        // Assert
        $this->assertNotNull($fetched);
        $this->assertSame($payload['title'], $fetched['title']);
        $this->assertSame($row['body'],      $fetched['body']);
        $this->assertSame($row['date'],      $fetched['date']);
    }

    /**
     * updateEntry() is a no-op when the target row does not exist.
     *
     * Scenario:
     * - Use a random, non-existent UUID.
     * - Call updateEntry() and then findById($id).
     *
     * Checks:
     * - Row is still absent (null) — no implicit upsert.
     *
     * @return void
     * @covers \Daylog\Infrastructure\Storage\Entries\EntryModel::updateEntry
     * @covers \Daylog\Infrastructure\Storage\Entries\EntryModel::findById
     */
    public function testUpdateEntryNoopWhenMissing(): void
    {
        // Arrange
        $model = new EntryModel($this->db);

        $id = UuidGenerator::generate();
        $payload = ['title' => 'Should not insert'];
        
        // Act
        $model->updateEntry($id, $payload);
        $fetched = $model->findById($id);

        // Assert
        $this->assertNull($fetched);
    }

    /**
     * deleteById() removes an existing row.
     *
     * Scenario:
     * - Insert a row, delete it by id, then attempt to fetch it again.
     *
     * Checks:
     * - findById($id) returns null after deletion.
     *
     * @return void
     * @covers \Daylog\Infrastructure\Storage\Entries\EntryModel::createEntry
     * @covers \Daylog\Infrastructure\Storage\Entries\EntryModel::deleteById
     * @covers \Daylog\Infrastructure\Storage\Entries\EntryModel::findById
     */
    public function testDeleteByIdRemovesRow(): void
    {
        // Arrange
        $model = new EntryModel($this->db);

        $data  = EntryTestData::getOne();
        $entry = Entry::fromArray($data);
        $row   = EntryFieldMapper::toDbRowFromEntry($entry);
        $model->createEntry($row);

        $id = $data['id'];
        // Act
        $model->deleteById($id);
        $fetched = $model->findById($id);

        // Assert
        $this->assertNull($fetched);
    }

    /**
     * deleteById() is a no-op for a missing UUID (idempotent).
     *
     * Scenario:
     * - Call deleteById($missingId) on an empty table, then count rows.
     *
     * Checks:
     * - countRows(null) remains 0.
     *
     * @return void
     * @covers \Daylog\Infrastructure\Storage\Entries\EntryModel::deleteById
     * @covers \Daylog\Infrastructure\Storage\Entries\EntryModel::countRows
     */
    public function testDeleteByIdNoopWhenMissing(): void
    {
        // Arrange
        $model = new EntryModel($this->db);

        $missingId = UuidGenerator::generate();

        // Act
        $model->deleteById($missingId);
        $count = $model->countRows(null);

        // Assert
        $this->assertSame(0, $count);
    }

    /**
     * findRows() returns plain arrays and respects order/limit/offset.
     *
     * Scenario:
     * - Insert three rows with different dates.
     * - Query with order = 'date DESC', limit = 2, offset = 0.
     *
     * Checks:
     * - Exactly two rows returned.
     * - Dates follow DESC order for the top two records.
     *
     * @return void
     * @covers \Daylog\Infrastructure\Storage\Entries\EntryModel::createEntry
     * @covers \Daylog\Infrastructure\Storage\Entries\EntryModel::findRows
     */
    public function testFindRowsReturnsPlainArraysWithOptions(): void
    {
        // Arrange
        $model = new EntryModel($this->db);

        $first = EntryTestData::getOne(date: '2025-09-09');
        $first = Entry::fromArray($first);

        $second = EntryTestData::getOne(date: '2025-09-10');
        $second = Entry::fromArray($second);

        $third = EntryTestData::getOne(date: '2025-09-11');
        $third = Entry::fromArray($third);

        $firstRow  = EntryFieldMapper::toDbRowFromEntry($first);
        $secondRow = EntryFieldMapper::toDbRowFromEntry($second);
        $thirdRow  = EntryFieldMapper::toDbRowFromEntry($third);

        $model->createEntry($firstRow);
        $model->createEntry($secondRow);
        $model->createEntry($thirdRow);

        $filter  = null;
        $options = [
            'order'  => 'date DESC',
            'limit'  => 2,
            'offset' => 0
        ];

        // Act
        $rows = $model->findRows($filter, $options);

        // Assert
        $this->assertCount(2, $rows);
        $this->assertSame($third->getDate(),  $rows[0]['date']);
        $this->assertSame($second->getDate(), $rows[1]['date']);
    }

    /**
     * countRows() returns the total number of rows when no filter is provided.
     *
     * Scenario:
     * - Insert two rows; call countRows(null).
     *
     * Checks:
     * - Returned total equals 2.
     *
     * @return void
     * @covers \Daylog\Infrastructure\Storage\Entries\EntryModel::createEntry
     * @covers \Daylog\Infrastructure\Storage\Entries\EntryModel::countRows
     */
    public function testCountRowsWithoutFilter(): void
    {
        // Arrange
        $model = new EntryModel($this->db);

        $first = EntryTestData::getOne(date: '2025-09-09');
        $first = Entry::fromArray($first);

        $second = EntryTestData::getOne(date: '2025-09-10');
        $second = Entry::fromArray($second);

        $firstRow  = EntryFieldMapper::toDbRowFromEntry($first);
        $secondRow = EntryFieldMapper::toDbRowFromEntry($second);

        $model->createEntry($firstRow);
        $model->createEntry($secondRow);

        // Act
        $total = $model->countRows(null);

        // Assert
        $this->assertSame(2, $total);
    }
}