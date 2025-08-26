<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Infrastructure\Storage\Entries;

use Codeception\Test\Unit;
use Daylog\Domain\Models\Entries\Entry;
use Daylog\Infrastructure\Storage\Entries\EntryModel;
use Daylog\Infrastructure\Storage\Entries\EntryStorage;
use Daylog\Domain\Services\UuidGenerator;
use Daylog\Tests\Support\Helper\EntryTestData;

/**
 * Class EntryStorageTest
 *
 * Verifies that EntryStorage::insert() returns a valid UUID v4.
 * Scenario: we inject a mocked EntryModel (DB is not touched), build a valid Entry,
 * pass a single time snapshot as $now, and assert UUID format. The test focuses on
 * storage-level orchestration (UUID generation + delegation to model).
 *
 * @covers \Daylog\Infrastructure\Storage\Entries\EntryStorage::insert
 */
final class EntryStorageTest extends Unit
{
    /**
     * Ensure insert() generates UUID v4 and delegates to model->create().
     *
     * Mechanics:
     * - Arrange: mock EntryModel; build Entry from helper; define $now.
     * - Act: call insert($entry, $now).
     * - Assert: UUID is valid v4; model->create() was called once with array payload.
     *
     * @return void
     */
    public function testInsertReturnsValidUuidV4(): void
    {
        /** Arrange **/
        $modelClass = EntryModel::class;
        $model      = $this->createMock($modelClass);

        $model
            ->expects($this->once())
            ->method('create')
            ->with($this->isType('array'));


        $storage = new EntryStorage($model);

        $data  = EntryTestData::getOne();
        $entry = Entry::fromArray($data);

        $now = '2025-08-12 10:00:00';

        /** Act **/
        $uuid = $storage->insert($entry, $now);

        /** Assert **/
        $isValid = UuidGenerator::isValid($uuid);
        $this->assertTrue($isValid);
    }
}
