<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Infrastructure\Storage\Entries;

use Codeception\Test\Unit;
use Daylog\Domain\Models\Entries\Entry;
use Daylog\Domain\Services\UuidGenerator;
use Daylog\Configuration\Bootstrap\SqlFactory;
use Daylog\Infrastructure\Storage\Entries\EntryModel;
use Daylog\Infrastructure\Storage\Entries\EntryStorage;
use Daylog\Tests\Support\Helper\EntryTestData;
use Daylog\Tests\Support\Fixture\EntryFixture;
use DB\SQL;

/**
 * Integration tests for EntryStorage: insert() and findById().
 *
 * Purpose:
 * Validate that EntryStorage integrates with F3 Mapper (EntryModel) and database:
 * - insert(): generates UUID v4 and persists the row.
 * - findById(): returns hydrated Entry when row exists, null when missing.
 *
 * Mechanics:
 * - Uses real EntryModel bound to shared DB connection in test bootstrap.
 * - insert(): persists via model->create() and mutates Entry with generated id.
 * - findById(): reads via model->findById() and hydrates Entry domain model.
 *
 * @covers \Daylog\Infrastructure\Storage\Entries\EntryStorage
 */
final class EntryStorageTest extends Unit
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
     * Ensures insert() generates a valid UUID v4 and row is persisted.
     *
     * @return void
     */
    public function testInsertPersistsAndGeneratesUuidV4(): void
    {
        // Arrange
        /** @var EntryModel $model */
        $model   = new EntryModel($this->db);        
        $storage = new EntryStorage($model);

        $data  = EntryTestData::getOne();
        $entry = Entry::fromArray($data);

        // Act
        $storage->insert($entry);

        // Assert: UUID assigned and valid v4
        $id      = $entry->getId();
        $isValid = UuidGenerator::isValid($id);
        $this->assertTrue($isValid);

        // Assert: row exists in DB and fields match
        $row = $model->findById($id);
        $this->assertNotNull($row);
        $this->assertSame($entry->getTitle(), $row['title']);
        $this->assertSame($entry->getBody(),  $row['body']);
        $this->assertSame($entry->getDate(),  $row['date']);
    }

    /**
     * Ensures findById() returns Entry when the row exists (happy path).
     *
     * Mechanics:
     * - Seed one row via EntryFixture::insertOne().
     * - Call storage->findById() and verify hydrated Entry.
     *
     * @return void
     */
    public function testFindByIdReturnsEntryWhenFound(): void
    {
        // Arrange
        $rows = EntryFixture::insertRows(1);
        $row  = $rows[0];
        $id   = $row['id'];

        $model   = new EntryModel($this->db);
        $storage = new EntryStorage($model);

        // Act
        $entity = $storage->findById($id);

        // Assert
        $this->assertSame($id,              $entity->getId());
        $this->assertSame($row['title'],    $entity->getTitle());
        $this->assertSame($row['body'],     $entity->getBody());
        $this->assertSame($row['date'],     $entity->getDate());
    }

    /**
     * Ensures findById() returns null when the row does not exist.
     *
     * @return void
     */
    public function testFindByIdReturnsNullWhenMissing(): void
    {
        // Arrange
        $model   = new EntryModel($this->db);
        $storage = new EntryStorage($model);

        $missingId = UuidGenerator::generate();

        // Act
        $result = $storage->findById($missingId);

        // Assert
        $this->assertNull($result);
    }

  /**
     * Ensures deleteById() removes existing row.
     *
     * @return void
     */
    public function testDeleteByIdRemovesExistingRow(): void
    {
        // Arrange
        $model   = new EntryModel($this->db);
        $storage = new EntryStorage($model);

        $rows = EntryFixture::insertRows(1);
        $row  = $rows[0];
        $id   = $row['id'];

        $before = $model->findById($id);
        $this->assertNotNull($before);

        // Act
        $storage = new EntryStorage($model);
        $storage->deleteById($id);

        // Assert
        $after = $model->findById($id);
        $this->assertNull($after);
    }

    /**
     * Ensures deleteById() is a no-op for a missing UUID.
     *
     * @return void
     */
    public function testDeleteByIdNoOpWhenMissing(): void
    {
        // Arrange
        $model   = new EntryModel($this->db);
        $storage = new EntryStorage($model);

        $missingId = UuidGenerator::generate();

        $probe = $model->findById($missingId);
        $this->assertNull($probe);

        // Act: should not throw
        $storage->deleteById($missingId);

        // Assert: still missing
        $after = $model->findById($missingId);
        $this->assertNull($after);
    }    
}
